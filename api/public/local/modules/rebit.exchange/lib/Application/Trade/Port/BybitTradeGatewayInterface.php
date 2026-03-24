<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Port;

use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для взаимодействия со сделками через Bybit API.
 */
interface BybitTradeGatewayInterface
{
    /**
     * Получить активные ордера. POST /v5/p2p/order/pending/simplifyList
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    public function fetchActiveOrders(int $userId, int $page = 1, int $size = 30): array;

    /**
     * Получить все ордера. POST /v5/p2p/order/simplifyList
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    public function fetchAllOrders(int $userId, int $page = 1, int $size = 30): array;

    /**
     * Получить детали ордера. POST /v5/p2p/order/info
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    public function fetchOrderInfo(int $userId, string $orderId): array;

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
