<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Shared\Dto;

final readonly class BybitCredentialsDto
{
    public function __construct(
        public string $apiKey,
        public string $apiSecret,
    ) {}
}
