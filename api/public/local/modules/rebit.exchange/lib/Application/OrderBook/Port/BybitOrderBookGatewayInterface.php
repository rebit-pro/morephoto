<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\Port;

use Rebit\Exchange\Application\OrderBook\Dto\Bybit\BybitOrderBookListDto;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для получения стакана из Bybit API.
 * POST /v5/p2p/item/online
 */
interface BybitOrderBookGatewayInterface
{
    /**
     * @throws HttpException
     */
    public function fetchOrderBook(
        int $userId,
        string $tokenId,
        string $currencyId,
        string $side,
        int $page = 1,
        int $size = 30,
    ): BybitOrderBookListDto;
}
