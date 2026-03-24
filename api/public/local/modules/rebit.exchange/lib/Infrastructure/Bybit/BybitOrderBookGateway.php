<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\OrderBook\Port\BybitOrderBookGatewayInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Адаптер для получения стакана P2P через Bybit API.
 * POST /v5/p2p/item/online
 */
final readonly class BybitOrderBookGateway implements BybitOrderBookGatewayInterface
{
    private const string ENDPOINT = '/v5/p2p/item/online';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function fetchOrderBook(
        int $userId,
        string $tokenId,
        string $currencyId,
        string $side,
        int $page = 1,
        int $size = 30,
    ): array {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->post(
                self::ENDPOINT,
                $connection->credentials,
                $connection->environment,
                [
                    'tokenId' => $tokenId,
                    'currencyId' => $currencyId,
                    'side' => $side,
                    'page' => (string)$page,
                    'size' => (string)$size,
                ],
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                'Ошибка получения стакана из Bybit: ' . $e->getMessage(),
                502,
            );
        }

        return $response->result['items'] ?? [];
    }
}
