<?php

declare(strict_types=1);

namespace Rebit\Wallet\Infrastructure\Bybit;

use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Wallet\Application\Balance\Port\BybitBalanceGatewayInterface;

/**
 * Адаптер для получения балансов через Bybit API.
 * Инкапсулирует endpoint, параметры запроса и парсинг ответа.
 */
final readonly class BybitBalanceGateway implements BybitBalanceGatewayInterface
{
    private const string WALLET_BALANCE_ENDPOINT = '/v5/account/wallet-balance';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function fetchBalances(int $userId): array
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

        return $this->extractCoins($response->result);
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
}
