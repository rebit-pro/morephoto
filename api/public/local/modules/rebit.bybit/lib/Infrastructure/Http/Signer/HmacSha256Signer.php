<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Signer;

final class HmacSha256Signer implements HmacSignerInterface
{
    public function sign(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }
}
