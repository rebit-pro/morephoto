<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class TradeMessageListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, TradeMessageResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
