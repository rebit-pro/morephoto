<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Service;

use Rebit\Wallet\Domain\Balance\Exception\InsufficientFundsException;
use Rebit\Wallet\Domain\Balance\Exception\InsufficientLockedFundsException;

/**
 * Доменный сервис для расчётов по балансам.
 *
 * Инварианты:
 * - available + locked = total (всегда)
 * - Блокировка возможна только при available >= amount
 */
final readonly class BalanceCalculator
{
    /**
     * Проверяет возможность блокировки средств.
     *
     * @throws InsufficientFundsException если недостаточно средств
     */
    public function assertCanLock(float $available, float $amount): void
    {
        if ($available < $amount) {
            throw new InsufficientFundsException($available, $amount);
        }
    }

    /**
     * Проверяет возможность разблокировки средств.
     *
     * @throws InsufficientLockedFundsException если заблокировано меньше, чем запрошено
     */
    public function assertCanUnlock(float $locked, float $amount): void
    {
        if ($locked < $amount) {
            throw new InsufficientLockedFundsException($locked, $amount);
        }
    }

    /**
     * Рассчитывает total на основе available и locked.
     */
    public function calculateTotal(float $available, float $locked): float
    {
        return $available + $locked;
    }

    /**
     * Определяет расхождение между локальным и Bybit балансами.
     */
    public function detectDiscrepancy(float $localTotal, float $bybitTotal, float $threshold = 0.00000001): bool
    {
        return abs($localTotal - $bybitTotal) > $threshold;
    }
}
