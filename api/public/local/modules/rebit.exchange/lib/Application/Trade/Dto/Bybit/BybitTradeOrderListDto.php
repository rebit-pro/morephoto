<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Bybit;

final readonly class BybitTradeOrderListDto
{
    /**
     * @param list<BybitTradeOrderSummaryDto> $items
     */
    public function __construct(
        public int $count,
        public array $items,
    ) {}
}
