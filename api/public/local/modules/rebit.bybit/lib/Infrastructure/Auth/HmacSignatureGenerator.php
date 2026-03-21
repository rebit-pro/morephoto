<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Auth;

final readonly class HmacSignatureGenerator
{
    /**
     * Генерирует HMAC-SHA256 подпись для Bybit API.
     *
     * GET: timestamp + apiKey + recvWindow + queryString
     * POST: timestamp + apiKey + recvWindow + jsonBody
     */
    public function generate(
        string $apiSecret,
        string $timestamp,
        string $apiKey,
        string $recvWindow,
        string $payload,
    ): string {
        $plainText = $timestamp . $apiKey . $recvWindow . $payload;

        return hash_hmac('sha256', $plainText, $apiSecret);
    }
}
