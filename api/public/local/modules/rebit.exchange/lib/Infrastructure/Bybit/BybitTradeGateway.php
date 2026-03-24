<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Адаптер для управления сделками через Bybit P2P API.
 */
final readonly class BybitTradeGateway implements BybitTradeGatewayInterface
{
    private const string ACTIVE_ORDERS_ENDPOINT = '/v5/p2p/order/pending/simplifyList';
    private const string ALL_ORDERS_ENDPOINT = '/v5/p2p/order/simplifyList';
    private const string ORDER_INFO_ENDPOINT = '/v5/p2p/order/info';
    private const string PAY_ENDPOINT = '/v5/p2p/order/pay';
    private const string FINISH_ENDPOINT = '/v5/p2p/order/finish';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    public function fetchActiveOrders(int $userId, int $page = 1, int $size = 30): array
    {
        return $this->post($userId, self::ACTIVE_ORDERS_ENDPOINT, [
            'page' => $page,
            'size' => $size,
        ]);
    }

    public function fetchAllOrders(int $userId, int $page = 1, int $size = 30): array
    {
        return $this->post($userId, self::ALL_ORDERS_ENDPOINT, [
            'page' => $page,
            'size' => $size,
        ]);
    }

    public function fetchOrderInfo(int $userId, string $orderId): array
    {
        return $this->post($userId, self::ORDER_INFO_ENDPOINT, [
            'orderId' => $orderId,
        ]);
    }

    public function confirmPayment(int $userId, string $orderId, string $paymentType, string $paymentId): void
    {
        $this->post($userId, self::PAY_ENDPOINT, [
            'orderId' => $orderId,
            'paymentType' => $paymentType,
            'paymentId' => $paymentId,
        ]);
    }

    public function releaseAssets(int $userId, string $orderId): void
    {
        $this->post($userId, self::FINISH_ENDPOINT, [
            'orderId' => $orderId,
        ]);
    }

    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    private function post(int $userId, string $endpoint, array $body): array
    {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->post(
                $endpoint,
                $connection->credentials,
                $connection->environment,
                $body,
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                "Bybit API error [{$endpoint}]: " . $e->getMessage(),
                502,
            );
        }

        return $response->result;
    }
}
