<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Адаптер для отправки сообщений в чат через Bybit P2P API.
 * POST /v5/p2p/order/message/send
 */
final readonly class BybitChatGateway implements BybitChatGatewayInterface
{
    private const string SEND_ENDPOINT = '/v5/p2p/order/message/send';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    public function sendMessage(
        int $userId,
        string $orderId,
        string $message,
        string $contentType,
        string $msgUuid,
        ?string $fileName = null,
    ): void {
        $connection = $this->connectionResolver->resolve($userId);

        $body = [
            'orderId' => $orderId,
            'message' => $message,
            'contentType' => $contentType,
            'msgUuid' => $msgUuid,
        ];

        if (null !== $fileName && '' !== $fileName) {
            $body['fileName'] = $fileName;
        }

        try {
            $this->bybitClient->post(
                self::SEND_ENDPOINT,
                $connection->credentials,
                $connection->environment,
                $body,
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                'Ошибка отправки сообщения в Bybit: ' . $e->getMessage(),
                502,
            );
        }
    }
}
