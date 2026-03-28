<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderInfoDto;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderListDto;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderSummaryDto;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradePaymentTermDto;
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

    public function fetchActiveOrders(int $userId, int $page = 1, int $size = 30): BybitTradeOrderListDto
    {
        return $this->mapOrderList(
            $this->post($userId, self::ACTIVE_ORDERS_ENDPOINT, [
                'page' => $page,
                'size' => $size,
            ]),
        );
    }

    public function fetchAllOrders(int $userId, int $page = 1, int $size = 30): BybitTradeOrderListDto
    {
        return $this->mapOrderList(
            $this->post($userId, self::ALL_ORDERS_ENDPOINT, [
                'page' => $page,
                'size' => $size,
            ]),
        );
    }

    public function fetchOrderInfo(int $userId, string $orderId): BybitTradeOrderInfoDto
    {
        /** @var array<string, mixed> $result */
        $result = $this->post($userId, self::ORDER_INFO_ENDPOINT, [
            'orderId' => $orderId,
        ]);

        /** @var array<int, array<string, mixed>> $paymentTermList */
        $paymentTermList = is_array($result['paymentTermList'] ?? null)
            ? $result['paymentTermList']
            : [];

        return new BybitTradeOrderInfoDto(
            id: (string)($result['id'] ?? ''),
            side: (int)($result['side'] ?? 0),
            itemId: (string)($result['itemId'] ?? ''),
            userId: (string)($result['userId'] ?? ''),
            nickName: (string)($result['nickName'] ?? ''),
            makerUserId: (string)($result['makerUserId'] ?? ''),
            targetUserId: (string)($result['targetUserId'] ?? ''),
            targetNickName: (string)($result['targetNickName'] ?? ''),
            tokenId: (string)($result['tokenId'] ?? ''),
            currencyId: (string)($result['currencyId'] ?? ''),
            price: (string)($result['price'] ?? ''),
            quantity: (string)($result['quantity'] ?? ''),
            amount: (string)($result['amount'] ?? ''),
            paymentType: (int)($result['paymentType'] ?? 0),
            transferDate: (string)($result['transferDate'] ?? ''),
            status: (int)($result['status'] ?? 0),
            createDate: (string)($result['createDate'] ?? ''),
            paymentTermList: array_map(
                static fn(array $paymentTerm): BybitTradePaymentTermDto => new BybitTradePaymentTermDto(
                    id: (string)($paymentTerm['id'] ?? ''),
                    realName: (string)($paymentTerm['realName'] ?? ''),
                    paymentType: (int)($paymentTerm['paymentType'] ?? 0),
                    bankName: (string)($paymentTerm['bankName'] ?? ''),
                    branchName: (string)($paymentTerm['branchName'] ?? ''),
                    accountNo: (string)($paymentTerm['accountNo'] ?? ''),
                    qrcode: (string)($paymentTerm['qrcode'] ?? ''),
                ),
                $paymentTermList,
            ),
            remark: (string)($result['remark'] ?? ''),
            transferLastSeconds: (string)($result['transferLastSeconds'] ?? ''),
        );
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

    /**
     * @param array<string, mixed> $result
     */
    private function mapOrderList(array $result): BybitTradeOrderListDto
    {
        /** @var array<int, array<string, mixed>> $items */
        $items = is_array($result['items'] ?? null)
            ? $result['items']
            : [];

        return new BybitTradeOrderListDto(
            count: (int)($result['count'] ?? count($items)),
            items: array_map(
                static fn(array $item): BybitTradeOrderSummaryDto => new BybitTradeOrderSummaryDto(
                    id: (string)($item['id'] ?? ''),
                    side: (int)($item['side'] ?? 0),
                    amount: (string)($item['amount'] ?? ''),
                    price: (string)($item['price'] ?? ''),
                    fee: (string)($item['fee'] ?? ''),
                    targetNickName: (string)($item['targetNickName'] ?? ''),
                    targetUserId: (string)($item['targetUserId'] ?? ''),
                    status: (int)($item['status'] ?? 0),
                    createDate: (string)($item['createDate'] ?? ''),
                    transferLastSeconds: (string)($item['transferLastSeconds'] ?? ''),
                ),
                $items,
            ),
        );
    }
}
