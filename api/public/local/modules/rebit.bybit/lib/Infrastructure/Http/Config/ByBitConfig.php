<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Config;

final readonly class ByBitConfig
{
    public function __construct(
        private string $apiKey,
        private string $apiSecret, // Нужен для HMAC_SHA256 подписи
        private string $baseUrl = 'https://api.bybit.com',
        private int $recvWindow = 5000,
    ) {}

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getRecvWindow(): int
    {
        return $this->recvWindow;
    }
}
