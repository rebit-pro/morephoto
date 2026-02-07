<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http;

interface RebitHttpResponseInterface
{
    public function getStatusCode(): int;

    public function getBody(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
