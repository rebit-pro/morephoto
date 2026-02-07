<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Dto;

final readonly class ByBitRequestDto
{
    /**
     * @param array<string, mixed>  $params
     * @param array<string, string> $headers
     */
    public function __construct(
        public string $method,
        public string $endpoint,
        public array $params = [],
        public array $headers = [],
    ) {}

    public function isPost(): bool
    {
        return 'POST' === strtoupper($this->method);
    }

    public function isGet(): bool
    {
        return 'GET' === strtoupper($this->method);
    }
}
