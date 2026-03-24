<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class OrderBookListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, OrderBookEntryResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
