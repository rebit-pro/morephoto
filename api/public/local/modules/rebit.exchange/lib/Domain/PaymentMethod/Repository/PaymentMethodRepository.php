<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\PaymentMethod\Repository;

use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod;
use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection;
use Rebit\Exchange\Domain\PaymentMethod\Entity\Table\PaymentMethodTable;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class PaymentMethodRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findActive(): PaymentMethodCollection
    {
        return $this->query(
            fn(): PaymentMethodCollection => PaymentMethodTable::query()
                ->setSelect(['*'])
                ->where('UF_IS_ACTIVE', 1)
                ->setOrder(['UF_SORT' => 'ASC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByCode(string $code): ?PaymentMethod
    {
        return $this->query(
            fn(): ?PaymentMethod => PaymentMethodTable::query()
                ->setSelect(['*'])
                ->where('UF_CODE', $code)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?PaymentMethod
    {
        return $this->query(
            fn(): ?PaymentMethod => PaymentMethodTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByBybitId(int $bybitId): ?PaymentMethod
    {
        return $this->query(
            fn(): ?PaymentMethod => PaymentMethodTable::query()
                ->setSelect(['*'])
                ->where('UF_BYBIT_ID', $bybitId)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * Возвращает маппинг Bybit-ID → UF_CODE для указанных Bybit-идентификаторов.
     *
     * Используется в GetOrderBookUseCase для замены числовых Bybit payment IDs
     * на человекочитаемые коды из локального справочника.
     *
     * @param array<int, int|string> $bybitIds
     *
     * @return array<string, string> ключ — Bybit ID (строка), значение — UF_CODE
     *
     * @throws RepositoryException
     */
    public function mapBybitIdsToCode(array $bybitIds): array
    {
        if ([] === $bybitIds) {
            return [];
        }

        return $this->query(function() use ($bybitIds): array {
            $intIds = array_map('intval', $bybitIds);

            $result = PaymentMethodTable::query()
                ->setSelect(['UF_BYBIT_ID', 'UF_CODE'])
                ->whereIn('UF_BYBIT_ID', $intIds)
                ->exec()
            ;

            $map = [];
            while ($row = $result->fetch()) {
                if (null !== $row['UF_BYBIT_ID']) {
                    $map[(string)$row['UF_BYBIT_ID']] = (string)$row['UF_CODE'];
                }
            }

            return $map;
        });
    }
}
