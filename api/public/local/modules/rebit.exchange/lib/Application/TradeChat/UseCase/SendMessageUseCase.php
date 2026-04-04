<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Ramsey\Uuid\Uuid;
use Rebit\Exchange\Application\TradeChat\Dto\Request\SendMessageRequestDto;
use Rebit\Exchange\Application\TradeChat\Dto\Result\TradeMessageResultDto;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Shared\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Share\Shared\Exception\ValidationHttpException;

/**
 * Отправка сообщения в чат сделки через Bybit + локальное сохранение.
 */
final readonly class SendMessageUseCase
{
    public function __construct(
        private TradeMessageRepository $messageRepository,
        private TradeRepository $tradeRepository,
        private BybitChatGatewayInterface $chatGateway,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(SendMessageRequestDto $dto, int $userId): TradeMessageResultDto
    {
        $trade = $this->tradeRepository->findById($dto->tradeId);

        if (null === $trade) {
            throw new EntityNotFoundException('Сделка не найдена');
        }

        if ($trade->getUfBuyerUserId() !== $userId && $trade->getUfSellerUserId() !== $userId) {
            throw new HttpException('Нет доступа к чату этой сделки', 403);
        }

        if ('' === trim($dto->message)) {
            throw new ValidationHttpException('Сообщение не может быть пустым');
        }

        $contentType = ContentTypeEnum::from($dto->contentType);
        $msgUuid = Uuid::uuid4()->toString();

        // Отправляем в Bybit
        $this->chatGateway->sendMessage(
            $userId,
            $trade->getUfBybitOrderId(),
            $dto->message,
            $contentType->value,
            $msgUuid,
            $dto->fileName,
        );

        // Сохраняем локально
        $msg = $this->messageRepository->create(
            tradeId: $dto->tradeId,
            userId: $userId,
            message: $dto->message,
            messageType: MessageTypeEnum::User,
            contentType: $contentType,
            bybitMsgUuid: $msgUuid,
            fileName: $dto->fileName,
        );

        return new TradeMessageResultDto(
            id: $msg->getId(),
            tradeId: $msg->getUfTradeId(),
            userId: $msg->getUfUserId(),
            message: $msg->getUfMessage(),
            messageType: $msg->getUfMessageType(),
            contentType: $msg->getUfContentType(),
            fileName: $msg->getUfFileName() ?: null,
            createdAt: $msg->getUfCreatedAt()?->format('c'),
        );
    }
}
