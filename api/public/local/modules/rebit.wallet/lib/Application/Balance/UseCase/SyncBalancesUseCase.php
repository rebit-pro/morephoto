<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\UseCase;

use Psr\Log\LoggerInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Domain\Balance\Dto\Result\BalanceListResultDto;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;

/**
 * Синхронизация балансов пользователя с Bybit.
 *
 * При расхождении — приоритет данным Bybit, расхождения логируются.
 */
final readonly class SyncBalancesUseCase
{
    private const string WALLET_BALANCE_ENDPOINT = '/v5/account/wallet-balance';

    public function __construct(
        private BalanceRepository $balanceRepository,
        private BalanceCalculator $balanceCalculator,
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $userId): BalanceListResultDto
    {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->get(
                self::WALLET_BALANCE_ENDPOINT,
                $connection->credentials,
                $connection->environment,
                ['accountType' => 'UNIFIED'],
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                'Ошибка синхронизации с Bybit: ' . $e->getMessage(),
                502,
            );
        }

        $coins = $this->extractCoins($response->result);

        $this->syncCoins($userId, $coins);

        return (new GetBalancesUseCase($this->balanceRepository))->execute($userId);
    }

    /**
     * Извлекает данные по монетам из ответа Bybit.
     *
     * @param array<string, mixed> $result
     *
     * @return array<int, array{
     *     coin: string,
     *     available: float,
     *     locked: float,
     *     total: float,
     * }>
     */
    private function extractCoins(array $result): array
    {
        $coins = [];

        $accounts = $result['list'] ?? [];

        foreach ($accounts as $account) {
            foreach ($account['coin'] ?? [] as $coinData) {
                $total = (float)($coinData['walletBalance'] ?? 0);
                $locked = (float)($coinData['locked'] ?? 0);

                $coins[] = [
                    'coin' => (string)($coinData['coin'] ?? ''),
                    'available' => $total - $locked,
                    'locked' => $locked,
                    'total' => $total,
                ];
            }
        }

        return $coins;
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
     *
     * @todo Маппинг coin → currencyId через CurrencyRepository (rebit.exchange).
     *       Сейчас используется заглушка — currencyId не резолвится.
     */
    private function syncCoins(int $userId, array $coins): void
    {
        foreach ($coins as $coin) {
            // @todo Получить currencyId из CurrencyRepository по коду монеты
            $currencyId = $this->resolveCurrencyId($coin['coin']);

            if (null === $currencyId) {
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

    /**
     * @todo Реализовать через CurrencyRepository из rebit.exchange.
     */
    private function resolveCurrencyId(string $coinCode): ?int
    {
        // Заглушка: маппинг будет реализован после создания модуля rebit.exchange
        // и HL-блока RebitCurrency
        return null;
    }
}
