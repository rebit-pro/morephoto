<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class TradeListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, TradeResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
