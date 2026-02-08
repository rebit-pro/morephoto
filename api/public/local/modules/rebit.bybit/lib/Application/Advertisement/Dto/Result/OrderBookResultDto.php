<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Result;

use Rebit\Share\Shared\Interface\ResultDtoInterface;

final class OrderBookResultDto implements ResultDtoInterface
{
    /**
     * @param AdItemDto[] $items
     */
    public function __construct(
        public readonly int $count,
        public array $items,
    ) {}
}

