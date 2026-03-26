<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\UseCase;

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
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId): BalanceListResultDto
    {
        $collection = $this->balanceRepository->findByUserId($userId);

        $balances = array_map(
            fn(Balance $balance): BalanceResultDto => new BalanceResultDto(
                id: $balance->getId(),
                userId: $balance->getUfUserId(),
                currencyId: $balance->getUfCurrencyId(),
                currency: $this->currencyQuery->findCodeById($balance->getUfCurrencyId()) ?? "CUR_{$balance->getUfCurrencyId()}",
                available: $balance->getUfAvailable(),
                locked: $balance->getUfLocked(),
                total: $balance->getUfTotal(),
                syncedAt: $balance->getUfSyncedAt()?->format('c'),
            ),
            $collection->getAll(),
        );

        return new BalanceListResultDto($balances);
    }
}
