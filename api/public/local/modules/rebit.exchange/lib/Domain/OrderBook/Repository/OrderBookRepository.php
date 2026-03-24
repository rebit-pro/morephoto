<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\OrderBook\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection;
use Rebit\Exchange\Domain\OrderBook\Entity\Table\OrderBookEntryTable;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class OrderBookRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findByCurrencyPairAndSide(int $currencyPairId, string $side): OrderBookEntryCollection
    {
        return $this->query(
            fn(): OrderBookEntryCollection => OrderBookEntryTable::query()
                ->setSelect(['*'])
                ->where('UF_CURRENCY_PAIR_ID', $currencyPairId)
                ->where('UF_SIDE', $side)
                ->setOrder(['UF_PRICE' => 'ASC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByBybitOrderId(string $bybitOrderId): ?OrderBookEntry
    {
        return $this->query(
            fn(): ?OrderBookEntry => OrderBookEntryTable::query()
                ->setSelect(['*'])
                ->where('UF_BYBIT_ORDER_ID', $bybitOrderId)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * Полная перезапись стакана для пары + направления.
     *
     * @param array<int, array{
     *     bybitOrderId: string,
     *     currencyPairId: int,
     *     side: string,
     *     price: float,
     *     quantity: float,
     *     minAmount: float,
     *     maxAmount: float,
     *     counterpartyName: string,
     *     counterpartyRating: float,
     *     counterpartyTrades: int,
     *     counterpartyCompletionRate: float,
     *     paymentMethodIds: string,
     *     paymentTimeLimit: int,
     * }> $entries
     *
     * @throws RepositoryException
     */
    public function replaceByCurrencyPairAndSide(int $currencyPairId, string $side, array $entries): void
    {
        $this->deleteByCurrencyPairAndSide($currencyPairId, $side);

        $now = new DateTime();

        foreach ($entries as $entry) {
            /** @var OrderBookEntry $entity */
            $entity = OrderBookEntryTable::createObject()
                ->setUfBybitOrderId($entry['bybitOrderId'])
                ->setUfCurrencyPairId($entry['currencyPairId'])
                ->setUfSide($entry['side'])
                ->setUfPrice($entry['price'])
                ->setUfQuantity($entry['quantity'])
                ->setUfMinAmount($entry['minAmount'])
                ->setUfMaxAmount($entry['maxAmount'])
                ->setUfCounterpartyName($entry['counterpartyName'])
                ->setUfCounterpartyRating($entry['counterpartyRating'])
                ->setUfCounterpartyTrades($entry['counterpartyTrades'])
                ->setUfCounterpartyCompletionRate($entry['counterpartyCompletionRate'])
                ->setUfPaymentMethodIds($entry['paymentMethodIds'])
                ->setUfPaymentTimeLimit($entry['paymentTimeLimit'])
                ->setUfSyncedAt($now)
            ;

            $this->persist($entity);
        }
    }

    /**
     * @throws RepositoryException
     */
    public function deleteByCurrencyPairAndSide(int $currencyPairId, string $side): void
    {
        $existing = $this->findByCurrencyPairAndSide($currencyPairId, $side);

        foreach ($existing as $entry) {
            $this->query(
                static function() use ($entry): void {
                    OrderBookEntryTable::delete($entry->getId());
                },
            );
        }
    }

    /**
     * Удаляет записи стакана старше указанного количества минут.
     *
     * @throws RepositoryException
     */
    public function deleteStale(int $staleMinutes = 5): int
    {
        $threshold = (new DateTime())->add("-{$staleMinutes} minutes");

        $staleEntries = $this->query(
            fn(): OrderBookEntryCollection => OrderBookEntryTable::query()
                ->setSelect(['ID'])
                ->where('UF_SYNCED_AT', '<', $threshold)
                ->exec()
                ->fetchCollection(),
        );

        $deleted = 0;
        foreach ($staleEntries as $entry) {
            $this->query(
                static function() use ($entry): void {
                    OrderBookEntryTable::delete($entry->getId());
                },
            );
            ++$deleted;
        }

        return $deleted;
    }
}
