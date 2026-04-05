<?php

declare(strict_types=1);

namespace Rebit\Wallet\Infrastructure\Bybit;

use Psr\Log\LoggerInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Wallet\Application\Balance\Dto\Bybit\BybitBalanceDto;
use Rebit\Wallet\Application\Balance\Dto\Bybit\BybitBalanceListDto;
use Rebit\Wallet\Application\Balance\Port\BybitBalanceGatewayInterface;

/**
 * Адаптер для получения балансов через Bybit API.
 * Инкапсулирует endpoint, параметры запроса и парсинг ответа.
 *
 * Используется endpoint /v5/asset/transfer/query-account-coins-balance с accountType=FUND.
 * Для P2P-операций балансы хранятся именно в FUND-аккаунте.
 * Ответ содержит result.balance[], а не result.list[].coin[].
 */
final readonly class BybitBalanceGateway implements BybitBalanceGatewayInterface
{
    private const string WALLET_BALANCE_ENDPOINT = '/v5/asset/transfer/query-account-coins-balance';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
        private LoggerInterface $logger,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function fetchBalances(int $userId): BybitBalanceListDto
    {
        $connection = $this->connectionResolver->resolve($userId);
        try {
            $response = $this->bybitClient->get(
                self::WALLET_BALANCE_ENDPOINT,
                $connection->credentials,
                $connection->environment,
                ['accountType' => 'FUND'],
            );
        } catch (BybitApiException $e) {
            $this->logger->error('Bybit balance API error', [
                'userId' => $userId,
                'message' => $e->getMessage(),
            ]);

            throw new HttpException(
                'Ошибка синхронизации с Bybit: ' . $e->getMessage(),
                502,
            );
        }

        $this->logger->info('Bybit balance raw response', [
            'userId' => $userId,
            'result' => $response->result,
        ]);

        $dto = $this->extractCoins($response->result);

        $this->logger->info('Bybit balance parsed', [
            'userId' => $userId,
            'coins' => array_map(
                static fn(BybitBalanceDto $b): array => [
                    'coin' => $b->coin,
                    'available' => $b->available,
                    'locked' => $b->locked,
                    'total' => $b->total,
                ],
                $dto->items,
            ),
        ]);

        return $dto;
    }

    /**
     * Извлекает данные по монетам из ответа Bybit.
     *
     * Ответ endpoint /v5/asset/transfer/query-account-coins-balance:
     * result.balance[].walletBalance  — общий баланс
     * result.balance[].transferBalance — доступно для вывода/перевода
     * locked = walletBalance - transferBalance
     *
     * @param array<string, mixed> $result
     */
    private function extractCoins(array $result): BybitBalanceListDto
    {
        $coins = [];

        /** @var array<int, array<string, mixed>> $balances */
        $balances = is_array($result['balance'] ?? null)
            ? $result['balance']
            : [];

        foreach ($balances as $coinData) {
            $total = (float)($coinData['walletBalance'] ?? 0);
            $available = (float)($coinData['transferBalance'] ?? 0);
            $locked = $total - $available;

            $coins[] = new BybitBalanceDto(
                coin: (string)($coinData['coin'] ?? ''),
                available: $available,
                locked: $locked,
                total: $total,
            );
        }

        return new BybitBalanceListDto(items: $coins);
    }
}
