<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Signer;

interface HmacSignerInterface
{
    public function sign(string $payload, string $secret): string;
}
