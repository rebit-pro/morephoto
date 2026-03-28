<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Port;

use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderInfoDto;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderListDto;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для взаимодействия со сделками через Bybit API.
 */
interface BybitTradeGatewayInterface
{
    /**
     * Получить активные ордера. POST /v5/p2p/order/pending/simplifyList
     *
     * @throws HttpException
     */
    public function fetchActiveOrders(int $userId, int $page = 1, int $size = 30): BybitTradeOrderListDto;

    /**
     * Получить все ордера. POST /v5/p2p/order/simplifyList
     *
     * @throws HttpException
     */
    public function fetchAllOrders(int $userId, int $page = 1, int $size = 30): BybitTradeOrderListDto;

    /**
     * Получить детали ордера. POST /v5/p2p/order/info
     *
     * @throws HttpException
     */
    public function fetchOrderInfo(int $userId, string $orderId): BybitTradeOrderInfoDto;

    /**
     * Отметить ордер как оплаченный. POST /v5/p2p/order/pay
     *
     * @throws HttpException
     */
    public function confirmPayment(int $userId, string $orderId, string $paymentType, string $paymentId): void;

    /**
     * Выпустить активы (подтвердить получение). POST /v5/p2p/order/finish
     *
     * @throws HttpException
     */
    public function releaseAssets(int $userId, string $orderId): void;
}
