<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Port;

use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для отправки сообщений в чат сделки через Bybit API.
 * POST /v5/p2p/order/message/send
 */
interface BybitChatGatewayInterface
{
    /**
     * @throws HttpException
     */
    public function sendMessage(
        int $userId,
        string $orderId,
        string $message,
        string $contentType,
        string $msgUuid,
        ?string $fileName = null,
    ): void;
}
