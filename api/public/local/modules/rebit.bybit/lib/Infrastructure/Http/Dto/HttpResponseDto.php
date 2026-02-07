<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Dto;

final class HttpResponseDto
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly string $body,
        public readonly array $headers = [],
    ) {}
}
