<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

/**
 * Value Object для учётных данных Bybit API.
 */
final readonly class BybitCredentials
{
    public string $apiKey;
    public string $apiSecret;

    public function __construct(string $apiKey, string $apiSecret)
    {
        if ('' === $apiKey) {
            throw new \InvalidArgumentException('API key must not be empty');
        }

        if ('' === $apiSecret) {
            throw new \InvalidArgumentException('API secret must not be empty');
        }

        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }
}
