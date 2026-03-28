<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Синхронизация входящих сообщений из Bybit P2P чата в локальную БД.
 * Вызывает POST /v5/p2p/order/message/queryList и сохраняет новые сообщения.
 */
final readonly class SyncChatMessagesUseCase
{
    public function __construct(
        private TradeMessageRepository $messageRepository,
        private BybitChatGatewayInterface $chatGateway,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(Trade $trade, int $userId): int
    {
        $orderId = $trade->getUfBybitOrderId();

        if ('' === $orderId) {
            return 0;
        }

        $messages = $this->chatGateway->fetchMessages($userId, $orderId);

        if ([] === $messages) {
            return 0;
        }

        $importedCount = 0;

        foreach ($messages as $msg) {
            $bybitMsgId = (string)($msg['id'] ?? '');

            if ('' === $bybitMsgId) {
                continue;
            }

            if ($this->messageRepository->existsByBybitMsgUuid($trade->getId(), $bybitMsgId)) {
                continue;
            }

            $contentType = ContentTypeEnum::tryFrom((string)($msg['contentType'] ?? 'str')) ?? ContentTypeEnum::Str;

            $this->messageRepository->create(
                tradeId: $trade->getId(),
                userId: 0,
                message: (string)($msg['message'] ?? ''),
                messageType: MessageTypeEnum::User,
                contentType: $contentType,
                bybitMsgUuid: $bybitMsgId,
                fileName: '' !== ($msg['fileName'] ?? '') ? (string)$msg['fileName'] : null,
            );

            $importedCount++;
        }

        return $importedCount;
    }
}
