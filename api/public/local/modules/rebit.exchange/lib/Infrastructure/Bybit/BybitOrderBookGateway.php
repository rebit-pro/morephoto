<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\OrderBook\Dto\Bybit\BybitOrderBookItemDto;
use Rebit\Exchange\Application\OrderBook\Dto\Bybit\BybitOrderBookListDto;
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
    ): BybitOrderBookListDto {
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

        /** @var array<int, array<string, mixed>> $items */
        $items = is_array($response->result['items'] ?? null)
            ? $response->result['items']
            : [];

        return new BybitOrderBookListDto(
            items: array_map(
                static fn(array $item): BybitOrderBookItemDto => new BybitOrderBookItemDto(
                    id: (string)($item['id'] ?? ''),
                    price: (string)($item['price'] ?? ''),
                    lastQuantity: (string)($item['lastQuantity'] ?? ''),
                    minAmount: (string)($item['minAmount'] ?? ''),
                    maxAmount: (string)($item['maxAmount'] ?? ''),
                    nickName: (string)($item['nickName'] ?? ''),
                    recentExecuteRate: (float)($item['recentExecuteRate'] ?? 0),
                    recentOrderNum: (int)($item['recentOrderNum'] ?? 0),
                    payments: self::normalizePayments($item['payments'] ?? []),
                    paymentPeriod: (int)($item['paymentPeriod'] ?? 0),
                    side: (int)($item['side'] ?? 0),
                ),
                $items,
            ),
        );
    }

    /**
     * @return list<string>
     */
    private static function normalizePayments(mixed $payments): array
    {
        if (!is_array($payments)) {
            return [];
        }

        return array_values(array_map(static fn(mixed $payment): string => (string)$payment, $payments));
    }
}
