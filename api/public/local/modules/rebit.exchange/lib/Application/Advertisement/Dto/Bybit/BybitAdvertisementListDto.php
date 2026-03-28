<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Bybit;

final readonly class BybitAdvertisementListDto
{
    /**
     * @param list<BybitAdvertisementItemDto> $items
     */
    public function __construct(
        public int $count,
        public array $items,
    ) {}
}
