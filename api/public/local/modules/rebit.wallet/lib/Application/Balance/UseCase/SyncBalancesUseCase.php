<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\UseCase;

use Psr\Log\LoggerInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Balance\Port\BybitBalanceGatewayInterface;
use Rebit\Wallet\Application\Balance\Dto\Result\BalanceListResultDto;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;

/**
 * Синхронизация балансов пользователя с Bybit.
 *
 * При расхождении — приоритет данным Bybit, расхождения логируются.
 */
final readonly class SyncBalancesUseCase
{
    public function __construct(
        private BalanceRepository $balanceRepository,
        private BalanceCalculator $balanceCalculator,
        private BybitBalanceGatewayInterface $balanceGateway,
        private CurrencyQueryInterface $currencyQuery,
        private GetBalancesUseCase $getBalancesUseCase,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $userId): BalanceListResultDto
    {
        $coins = $this->balanceGateway->fetchBalances($userId);
        $this->syncCoins($userId, $coins);

        return $this->getBalancesUseCase->execute($userId);
    }

    /**
     * Синхронизирует балансы по монетам с локальной базой.
     *
     * @param array<int, array{
     *     coin: string,
     *     available: float,
     *     locked: float,
     *     total: float,
     * }> $coins
     *
     * @throws RepositoryException
     */
    private function syncCoins(int $userId, array $coins): void
    {
        if ([] !== $coins) {
            $this->logger->info('Bybit coins received', [
                'userId' => $userId,
                'count' => count($coins),
                'coins' => array_column($coins, 'coin'),
            ]);
        }
        foreach ($coins as $coin) {
            $currencyId = $this->currencyQuery->findIdByCode($coin['coin']);
            if (null === $currencyId) {
                $this->logger->warning('Skipped unknown coin: currencyId not resolved', [
                    'userId' => $userId,
                    'coin' => $coin['coin'],
                    'total' => $coin['total'],
                ]);
                continue;
            }
            $existingBalance = $this->balanceRepository->findByUserIdAndCurrencyId($userId, $currencyId);
            if (null !== $existingBalance) {
                $localTotal = $existingBalance->getUfTotal();
                if ($this->balanceCalculator->detectDiscrepancy($localTotal, $coin['total'])) {
                    $this->logger->warning('BalanceDiscrepancy', [
                        'userId' => $userId,
                        'currencyId' => $currencyId,
                        'localTotal' => $localTotal,
                        'bybitTotal' => $coin['total'],
                        'difference' => abs($localTotal - $coin['total']),
                    ]);
                }
            }
            $this->balanceRepository->upsertFromSync(
                $userId,
                $currencyId,
                $coin['available'],
                $coin['locked'],
                $coin['total'],
            );
        }
    }
}
