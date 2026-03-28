<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Bybit;

final readonly class BybitAdvertisementCreateResultDto
{
    public function __construct(
        public string $itemId,
    ) {}
}
