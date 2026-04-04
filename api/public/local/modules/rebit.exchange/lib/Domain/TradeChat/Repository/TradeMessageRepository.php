<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\TradeChat\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage;
use Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection;
use Rebit\Exchange\Domain\TradeChat\Entity\Table\TradeMessageTable;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class TradeMessageRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findByTradeId(int $tradeId): TradeMessageCollection
    {
        return $this->query(
            fn(): TradeMessageCollection => TradeMessageTable::query()
                ->setSelect(['*'])
                ->where('UF_TRADE_ID', $tradeId)
                ->setOrder(['UF_CREATED_AT' => 'ASC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * Количество сообщений пользователя за последние N секунд (антиспам).
     *
     * @throws RepositoryException
     */
    public function countRecentByUser(int $tradeId, int $userId, int $seconds = 30): int
    {
        $threshold = (new DateTime())->add("-{$seconds} seconds");

        return $this->query(
            static function() use ($tradeId, $userId, $threshold): int {
                return (int)TradeMessageTable::query()
                    ->where('UF_TRADE_ID', $tradeId)
                    ->where('UF_USER_ID', $userId)
                    ->where('UF_CREATED_AT', '>', $threshold)
                    ->queryCountTotal()
                ;
            },
        );
    }

    /**
     * Проверяет, существует ли сообщение с данным Bybit UUID в рамках сделки.
     *
     * @throws RepositoryException
     */
    public function existsByBybitMsgUuid(int $tradeId, string $bybitMsgUuid): bool
    {
        if ('' === $bybitMsgUuid) {
            return false;
        }

        return $this->query(
            fn(): bool => 0 < TradeMessageTable::query()
                ->where('UF_TRADE_ID', $tradeId)
                ->where('UF_BYBIT_MSG_UUID', $bybitMsgUuid)
                ->setLimit(1)
                ->queryCountTotal(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function create(
        int $tradeId,
        int $userId,
        string $message,
        MessageTypeEnum $messageType,
        ContentTypeEnum $contentType,
        string $bybitMsgUuid,
        ?string $fileName = null,
        ?int $scriptStepId = null,
        ?DateTime $createdAt = null,
    ): TradeMessage {
        /** @var TradeMessage $msg */
        $msg = TradeMessageTable::createObject()
            ->setUfTradeId($tradeId)
            ->setUfUserId($userId)
            ->setUfMessage($message)
            ->setUfMessageType($messageType->value)
            ->setUfContentType($contentType->value)
            ->setUfBybitMsgUuid($bybitMsgUuid)
            ->setUfFileName($fileName ?? '')
            ->setUfScriptStepId($scriptStepId ?? 0)
            ->setUfIsRead(0)
            ->setUfCreatedAt($createdAt ?? new DateTime())
        ;

        $this->persist($msg);

        return $msg;
    }
}
