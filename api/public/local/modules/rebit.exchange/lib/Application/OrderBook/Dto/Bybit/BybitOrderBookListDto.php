<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\Dto\Bybit;

final readonly class BybitOrderBookListDto
{
    /**
     * @param list<BybitOrderBookItemDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
