<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\UseCase;

use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Balance\Dto\Result\BalanceListResultDto;
use Rebit\Wallet\Application\Balance\Dto\Result\BalanceResultDto;
use Rebit\Wallet\Domain\Balance\Entity\Balance;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;

final readonly class GetBalancesUseCase
{
    public function __construct(
        private BalanceRepository $balanceRepository,
        private CurrencyQueryInterface $currencyQuery,
        private CurrencyRubRateQueryInterface $currencyRubRateQuery,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId): BalanceListResultDto
    {
        $collection = $this->balanceRepository->findByUserId($userId);
        $totalRubEquivalent = 0.0;

        $balances = array_map(
            function(Balance $balance) use (&$totalRubEquivalent): BalanceResultDto {
                $currencyCode = $this->currencyQuery->findCodeById($balance->getUfCurrencyId()) ?? "CUR_{$balance->getUfCurrencyId()}";
                $rubRate = $this->currencyRubRateQuery->findApproximateRubRateByCurrencyCode($currencyCode);
                $rubEquivalent = null;

                if (null !== $rubRate && 0.0 < $rubRate) {
                    $rubEquivalent = $balance->getUfTotal() * $rubRate;
                    $totalRubEquivalent += $rubEquivalent;
                }

                return new BalanceResultDto(
                    id: $balance->getId(),
                    userId: $balance->getUfUserId(),
                    currencyId: $balance->getUfCurrencyId(),
                    currency: $currencyCode,
                    available: $balance->getUfAvailable(),
                    locked: $balance->getUfLocked(),
                    total: $balance->getUfTotal(),
                    rubRate: $rubRate,
                    rubEquivalent: $rubEquivalent,
                    syncedAt: $balance->getUfSyncedAt()?->format('c'),
                );
            },
            $collection->getAll(),
        );

        return new BalanceListResultDto(
            balances: $balances,
            totalRubEquivalent: [] === $balances ? null : $totalRubEquivalent,
        );
    }
}
