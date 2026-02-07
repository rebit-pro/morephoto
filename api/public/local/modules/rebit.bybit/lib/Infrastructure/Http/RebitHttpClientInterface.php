<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http;

interface RebitHttpClientInterface
{
    /**
     * @param array<string, string> $headers
     */
    public function get(string $url, array $headers = []): RebitHttpResponseInterface;

    /**
     * @param array<string, string>       $headers
     * @param array<string, mixed>|string $body
     */
    public function post(string $url, array $headers = [], array|string $body = []): RebitHttpResponseInterface;
}
