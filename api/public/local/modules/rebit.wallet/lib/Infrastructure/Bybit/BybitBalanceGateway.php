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
                ['accountType' => 'FUND'],
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
     * Ответ endpoint /v5/asset/transfer/query-account-coins-balance:
     * result.balance[].walletBalance  — общий баланс
     * result.balance[].transferBalance — доступно для вывода/перевода
     * locked = walletBalance - transferBalance
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
        foreach ($result['balance'] ?? [] as $coinData) {
            $total = (float)($coinData['walletBalance'] ?? 0);
            $available = (float)($coinData['transferBalance'] ?? 0);
            $locked = $total - $available;
            $coins[] = [
                'coin' => (string)($coinData['coin'] ?? ''),
                'available' => $available,
                'locked' => $locked,
                'total' => $total,
            ];
        }
        return $coins;
    }
}
