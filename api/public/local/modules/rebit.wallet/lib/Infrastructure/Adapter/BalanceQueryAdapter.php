<?php

declare(strict_types=1);

namespace Rebit\Wallet\Infrastructure\Adapter;

use Rebit\Share\Application\Contract\Wallet\BalanceQueryInterface;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;

/**
 * Адаптер для проверки доступного баланса пользователя.
 * Реализует контракт из rebit.share, делегирует запрос в BalanceRepository.
 */
final readonly class BalanceQueryAdapter implements BalanceQueryInterface
{
    public function __construct(
        private BalanceRepository $balanceRepository,
    ) {}

    public function hasAvailableBalance(int $userId, int $currencyId, float $requiredAmount): bool
    {
        $balance = $this->balanceRepository->findByUserIdAndCurrencyId($userId, $currencyId);

        if (null === $balance) {
            return false;
        }

        return $balance->getUfAvailable() >= $requiredAmount;
    }
}
