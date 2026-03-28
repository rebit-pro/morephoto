<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Bitrix\Main\Type\DateTime;
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

        if ([] === $messages->messages) {
            return 0;
        }

        $importedCount = 0;

        foreach ($messages->messages as $msg) {
            $bybitMsgId = $msg->id;

            if ('' === $bybitMsgId) {
                continue;
            }

            if ($this->messageRepository->existsByBybitMsgUuid($trade->getId(), $bybitMsgId)) {
                continue;
            }

            $contentType = ContentTypeEnum::tryFrom($msg->contentType) ?? ContentTypeEnum::Str;
            $createdAt = $this->resolveCreatedAt($msg->createDate);

            $this->messageRepository->create(
                tradeId: $trade->getId(),
                userId: 0,
                message: $msg->message,
                messageType: MessageTypeEnum::User,
                contentType: $contentType,
                bybitMsgUuid: $bybitMsgId,
                fileName: '' !== $msg->fileName ? $msg->fileName : null,
                createdAt: $createdAt,
            );

            ++$importedCount;
        }

        return $importedCount;
    }

    private function resolveCreatedAt(string $createDate): ?DateTime
    {
        if ('' === $createDate) {
            return null;
        }

        if (ctype_digit($createDate)) {
            $timestamp = (int)$createDate;

            if (10 < strlen($createDate)) {
                $timestamp = (int)floor($timestamp / 1000);
            }

            return DateTime::createFromTimestamp($timestamp);
        }

        $timestamp = strtotime($createDate);

        if (false === $timestamp) {
            return null;
        }

        return DateTime::createFromTimestamp($timestamp);
    }
}
