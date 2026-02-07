<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Config;

final class ByBitConfig
{
    private const int DEFAULT_RECV_WINDOW_MS = 5000;

    public function __construct(
        private readonly string $baseUri,
        private readonly string $apiKey,
        private readonly string $signatureSecret,
        private readonly int $recvWindowMs = self::DEFAULT_RECV_WINDOW_MS,
    ) {
        if ('' === trim($this->baseUri)) {
            throw new \InvalidArgumentException('ByBit baseUri is empty.');
        }

        if ('' === trim($this->apiKey)) {
            throw new \InvalidArgumentException('ByBit apiKey is empty.');
        }

        if ('' === trim($this->signatureSecret)) {
            throw new \InvalidArgumentException('ByBit signature secret is empty.');
        }

        if (0 >= $this->recvWindowMs) {
            throw new \InvalidArgumentException('ByBit recvWindowMs must be positive.');
        }
    }

    public function baseUri(): string
    {
        return $this->baseUri;
    }

    public function apiKey(): string
    {
        return $this->apiKey;
    }

    public function signatureSecret(): string
    {
        return $this->signatureSecret;
    }

    public function recvWindowMs(): int
    {
        return $this->recvWindowMs;
    }
}
