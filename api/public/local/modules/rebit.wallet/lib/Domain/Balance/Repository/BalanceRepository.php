<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Domain\Balance\Entity\Balance;
use Rebit\Wallet\Domain\Balance\Entity\BalanceCollection;
use Rebit\Wallet\Domain\Balance\Entity\Table\BalanceTable;

final class BalanceRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findByUserId(int $userId): BalanceCollection
    {
        return $this->query(
            fn(): BalanceCollection => BalanceTable::query()
                ->setSelect(['*'])
                ->where('UF_USER_ID', $userId)
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByUserIdAndCurrencyId(int $userId, int $currencyId): ?Balance
    {
        return $this->query(
            fn(): ?Balance => BalanceTable::query()
                ->setSelect(['*'])
                ->where('UF_USER_ID', $userId)
                ->where('UF_CURRENCY_ID', $currencyId)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function save(Balance $balance): void
    {
        $this->persist($balance);
    }

    /**
     * Блокировка средств: уменьшает available, увеличивает locked.
     *
     * @throws RepositoryException
     */
    public function lockFunds(Balance $balance, float $amount): void
    {
        $balance
            ->setUfAvailable($balance->getUfAvailable() - $amount)
            ->setUfLocked($balance->getUfLocked() + $amount)
            ->setUfUpdatedAt(new DateTime())
        ;

        $this->persist($balance);
    }

    /**
     * Разблокировка средств: увеличивает available, уменьшает locked.
     *
     * @throws RepositoryException
     */
    public function unlockFunds(Balance $balance, float $amount): void
    {
        $balance
            ->setUfAvailable($balance->getUfAvailable() + $amount)
            ->setUfLocked($balance->getUfLocked() - $amount)
            ->setUfUpdatedAt(new DateTime())
        ;

        $this->persist($balance);
    }

    /**
     * Обновление баланса при синхронизации с Bybit.
     *
     * @throws RepositoryException
     */
    public function upsertFromSync(
        int $userId,
        int $currencyId,
        float $available,
        float $locked,
        float $total,
    ): void {
        $balance = $this->findByUserIdAndCurrencyId($userId, $currencyId);
        $now = new DateTime();

        if (null === $balance) {
            /** @var Balance $balance */
            $balance = BalanceTable::createObject()
                ->setUfUserId($userId)
                ->setUfCurrencyId($currencyId)
            ;
        }

        $balance
            ->setUfAvailable($available)
            ->setUfLocked($locked)
            ->setUfTotal($total)
            ->setUfSyncedAt($now)
            ->setUfUpdatedAt($now)
        ;

        $this->persist($balance);
    }
}
