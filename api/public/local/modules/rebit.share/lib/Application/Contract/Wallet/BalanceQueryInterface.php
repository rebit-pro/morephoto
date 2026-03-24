<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Wallet;

/**
 * Контракт для проверки доступного баланса пользователя.
 * Реализация в модуле rebit.wallet.
 */
interface BalanceQueryInterface
{
    /**
     * Проверяет, достаточно ли доступных средств у пользователя.
     *
     * @param int   $userId         ID пользователя
     * @param int   $currencyId     ID валюты (rebit_currency.ID)
     * @param float $requiredAmount Необходимая сумма
     */
    public function hasAvailableBalance(int $userId, int $currencyId, float $requiredAmount): bool;
}
