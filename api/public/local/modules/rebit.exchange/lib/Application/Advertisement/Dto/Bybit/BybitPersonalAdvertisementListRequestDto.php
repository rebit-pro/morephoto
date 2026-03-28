<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Bybit;

final readonly class BybitPersonalAdvertisementListRequestDto
{
    public function __construct(
        public string $itemId = '',
        public string $status = '',
        public string $side = '',
        public string $tokenId = '',
        public string $page = '1',
        public string $size = '10',
        public string $currencyId = '',
    ) {}
}
