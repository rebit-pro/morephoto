<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Transaction\Repository;

use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\Type\DateTime;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Domain\Transaction\Entity\Table\TransactionTable;
use Rebit\Wallet\Domain\Transaction\ValueObject\TransactionFilter;
use Rebit\Wallet\Domain\Transaction\Entity\Transaction;
use Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;

/**
 * Репозиторий транзакций (append-only).
 * Транзакции не редактируются и не удаляются.
 */
final class TransactionRepository
{
    use RepositoryExceptionTrait;

    /**
     * Список транзакций с фильтрацией и пагинацией.
     *
     * @throws RepositoryException
     */
    public function findByFilter(int $userId, TransactionFilter $filter): TransactionCollection
    {
        return $this->query(function() use ($userId, $filter): TransactionCollection {
            $query = TransactionTable::query()
                ->setSelect(['*'])
                ->where('UF_USER_ID', $userId)
                ->setOrder(['UF_CREATED_AT' => 'DESC'])
                ->setLimit($filter->limit)
                ->setOffset($filter->offset)
            ;

            if (null !== $filter->type && '' !== $filter->type) {
                $query->where('UF_TYPE', $filter->type);
            }

            if (null !== $filter->currencyId) {
                $query->where('UF_CURRENCY_ID', $filter->currencyId);
            }

            if (null !== $filter->dateFrom && '' !== $filter->dateFrom) {
                $query->where('UF_CREATED_AT', '>=', new DateTime($filter->dateFrom, 'Y-m-d'));
            }

            if (null !== $filter->dateTo && '' !== $filter->dateTo) {
                $query->where('UF_CREATED_AT', '<=', new DateTime($filter->dateTo, 'Y-m-d'));
            }

            return $query->exec()->fetchCollection();
        });
    }

    /**
     * Общее количество транзакций по фильтру (для пагинации).
     *
     * @throws RepositoryException
     */
    public function countByFilter(int $userId, TransactionFilter $filter): int
    {
        return $this->query(function() use ($userId, $filter): int {
            $query = TransactionTable::query()
                ->addSelect(new ExpressionField('CNT', 'COUNT(*)'))
                ->where('UF_USER_ID', $userId)
            ;

            if (null !== $filter->type && '' !== $filter->type) {
                $query->where('UF_TYPE', $filter->type);
            }

            if (null !== $filter->currencyId) {
                $query->where('UF_CURRENCY_ID', $filter->currencyId);
            }

            if (null !== $filter->dateFrom && '' !== $filter->dateFrom) {
                $query->where('UF_CREATED_AT', '>=', new DateTime($filter->dateFrom, 'Y-m-d'));
            }

            if (null !== $filter->dateTo && '' !== $filter->dateTo) {
                $query->where('UF_CREATED_AT', '<=', new DateTime($filter->dateTo, 'Y-m-d'));
            }

            $row = $query->exec()->fetch();

            return (int)($row['CNT'] ?? 0);
        });
    }

    /**
     * Создание транзакции (append-only).
     *
     * @throws RepositoryException
     */
    public function create(
        int $userId,
        int $currencyId,
        TransactionTypeEnum $type,
        float $amount,
        float $balanceAfter,
        ?int $tradeId = null,
        ?string $description = null,
        ?string $bybitTxId = null,
    ): Transaction {
        $transaction = TransactionTable::createObject()
            ->setUfUserId($userId)
            ->setUfCurrencyId($currencyId)
            ->setUfType($type->value)
            ->setUfAmount($amount)
            ->setUfBalanceAfter($balanceAfter)
            ->setUfTradeId($tradeId)
            ->setUfDescription($description)
            ->setUfBybitTxId($bybitTxId)
            ->setUfCreatedAt(new DateTime())
        ;

        $this->persist($transaction);

        return $transaction;
    }

    /**
     * Суммирует обороты по валютам за период.
     *
     * Возвращает массив: currencyId => ['incoming' => float, 'outgoing' => float]
     *
     * @return array<int, array{
     *     incoming: float,
     *     outgoing: float,
     * }>
     *
     * @throws RepositoryException
     */
    public function sumTurnoverByCurrency(
        int $userId,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?int $currencyId = null,
    ): array {
        return $this->query(function() use ($userId, $dateFrom, $dateTo, $currencyId): array {
            $incomingTypes = TransactionTypeEnum::incomingValues();
            $outgoingTypes = TransactionTypeEnum::outgoingValues();

            $inList = "'" . implode("','", $incomingTypes) . "'";
            $outList = "'" . implode("','", $outgoingTypes) . "'";

            $query = TransactionTable::query()
                ->setSelect([
                    'UF_CURRENCY_ID',
                    new ExpressionField(
                        'INCOMING',
                        "SUM(CASE WHEN %s IN ({$inList}) THEN ABS(%s) ELSE 0 END)",
                        ['UF_TYPE', 'UF_AMOUNT'],
                    ),
                    new ExpressionField(
                        'OUTGOING',
                        "SUM(CASE WHEN %s IN ({$outList}) THEN ABS(%s) ELSE 0 END)",
                        ['UF_TYPE', 'UF_AMOUNT'],
                    ),
                ])
                ->where('UF_USER_ID', $userId)
                ->setGroup(['UF_CURRENCY_ID'])
            ;

            if (null !== $currencyId) {
                $query->where('UF_CURRENCY_ID', $currencyId);
            }

            if (null !== $dateFrom && '' !== $dateFrom) {
                $query->where('UF_CREATED_AT', '>=', new DateTime($dateFrom, 'Y-m-d'));
            }

            if (null !== $dateTo && '' !== $dateTo) {
                $query->where('UF_CREATED_AT', '<=', new DateTime($dateTo . ' 23:59:59', 'Y-m-d H:i:s'));
            }

            $result = [];
            foreach ($query->exec()->fetchAll() as $row) {
                $result[(int)$row['UF_CURRENCY_ID']] = [
                    'incoming' => (float)($row['INCOMING'] ?? 0),
                    'outgoing' => (float)($row['OUTGOING'] ?? 0),
                ];
            }

            return $result;
        });
    }

    /**
     * Получает баланс (balance_after) из последней транзакции ДО указанной даты.
     * Используется для определения «остатка на начало периода».
     *
     * @return array<int, float> currencyId => balanceAfter
     *
     * @throws RepositoryException
     */
    public function getBalanceBeforeDate(
        int $userId,
        string $date,
        ?int $currencyId = null,
    ): array {
        return $this->query(function() use ($userId, $date, $currencyId): array {
            $query = TransactionTable::query()
                ->setSelect([
                    'UF_CURRENCY_ID',
                    new ExpressionField('LAST_BALANCE', 'MAX(%s)', ['ID']),
                ])
                ->where('UF_USER_ID', $userId)
                ->where('UF_CREATED_AT', '<', new DateTime($date, 'Y-m-d'))
                ->setGroup(['UF_CURRENCY_ID'])
            ;

            if (null !== $currencyId) {
                $query->where('UF_CURRENCY_ID', $currencyId);
            }

            $result = [];
            foreach ($query->exec()->fetchAll() as $row) {
                $lastId = (int)$row['LAST_BALANCE'];

                $tx = TransactionTable::query()
                    ->setSelect(['UF_BALANCE_AFTER', 'UF_CURRENCY_ID'])
                    ->where('ID', $lastId)
                    ->exec()
                    ->fetch()
                ;

                if (false !== $tx) {
                    $result[(int)$row['UF_CURRENCY_ID']] = (float)$tx['UF_BALANCE_AFTER'];
                }
            }

            return $result;
        });
    }
}
