<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Result;

use Rebit\Share\Shared\Interface\ResultDtoInterface;

final class PersonalListResultDto implements ResultDtoInterface
{
    /**
     * @param PersonalAdItemDto[] $items
     */
    public function __construct(
        public readonly int $count,
        public readonly bool $hiddenFlag,
        public readonly array $items,
    ) {}
}

