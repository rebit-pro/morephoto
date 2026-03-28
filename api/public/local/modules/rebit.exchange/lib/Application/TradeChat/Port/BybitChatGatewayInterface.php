<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Port;

use Rebit\Exchange\Application\TradeChat\Dto\Bybit\BybitTradeChatMessageListDto;
use Rebit\Exchange\Application\TradeChat\Dto\Bybit\BybitTradeChatUploadResultDto;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для работы с чатом сделки через Bybit API.
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

    /**
     * @throws HttpException
     */
    public function uploadFile(
        int $userId,
        string $filePath,
        string $fileName,
        string $mimeType,
    ): BybitTradeChatUploadResultDto;

    /**
     * Получение сообщений из чата сделки (POST /v5/p2p/order/message/queryList).
     *
     * @throws HttpException
     */
    public function fetchMessages(
        int $userId,
        string $orderId,
        int $page = 1,
        int $size = 50,
    ): BybitTradeChatMessageListDto;
}
