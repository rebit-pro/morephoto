<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Service;

use Rebit\Share\Shared\Exception\HttpException;

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
     * @throws HttpException если недостаточно средств
     */
    public function assertCanLock(float $available, float $amount): void
    {
        if ($available < $amount) {
            throw new HttpException(
                sprintf(
                    'Недостаточно средств: доступно %.8f, запрошено %.8f',
                    $available,
                    $amount,
                ),
                422,
            );
        }
    }

    /**
     * Проверяет возможность разблокировки средств.
     *
     * @throws HttpException если заблокировано меньше, чем запрошено
     */
    public function assertCanUnlock(float $locked, float $amount): void
    {
        if ($locked < $amount) {
            throw new HttpException(
                sprintf(
                    'Невозможно разблокировать: заблокировано %.8f, запрошено %.8f',
                    $locked,
                    $amount,
                ),
                422,
            );
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
