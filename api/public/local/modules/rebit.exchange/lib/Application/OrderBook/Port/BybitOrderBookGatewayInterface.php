<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\Port;

use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для получения стакана из Bybit API.
 * POST /v5/p2p/item/online
 */
interface BybitOrderBookGatewayInterface
{
    /**
     * @return array<int, array{
     *     id: string,
     *     price: string,
     *     lastQuantity: string,
     *     minAmount: string,
     *     maxAmount: string,
     *     nickName: string,
     *     recentExecuteRate: string|int,
     *     recentOrderNum: string|int,
     *     payments: array<int, string>,
     *     paymentPeriod: int,
     *     side: int|string,
     * }>
     *
     * @throws HttpException
     */
    public function fetchOrderBook(
        int $userId,
        string $tokenId,
        string $currencyId,
        string $side,
        int $page = 1,
        int $size = 30,
    ): array;
}
