<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Contract;

use Rebit\Bybit\Infrastructure\Http\Dto\HttpResponseDto;

interface HttpClientInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function request(string $method, string $uri, ?string $body, array $headers): HttpResponseDto;
}
