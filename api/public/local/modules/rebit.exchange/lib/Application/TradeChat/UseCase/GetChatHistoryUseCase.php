<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Rebit\Exchange\Application\TradeChat\Dto\Result\TradeMessageListResultDto;
use Rebit\Exchange\Application\TradeChat\Dto\Result\TradeMessageResultDto;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение истории чата из локальной БД с предварительной синхронизацией из Bybit.
 */
final readonly class GetChatHistoryUseCase
{
    public function __construct(
        private TradeMessageRepository $messageRepository,
        private TradeRepository $tradeRepository,
        private SyncChatMessagesUseCase $syncChatMessages,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $tradeId, int $userId): TradeMessageListResultDto
    {
        $trade = $this->tradeRepository->findById($tradeId);

        if (null === $trade) {
            throw new EntityNotFoundException('Сделка не найдена');
        }

        if ($trade->getUfBuyerUserId() !== $userId && $trade->getUfSellerUserId() !== $userId) {
            throw new HttpException('Нет доступа к чату этой сделки', 403);
        }

        $this->syncChatMessages->execute($trade, $userId);

        $messages = $this->messageRepository->findByTradeId($tradeId);

        $items = [];
        foreach ($messages as $msg) {
            $items[] = new TradeMessageResultDto(
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

        return new TradeMessageListResultDto($items);
    }
}
