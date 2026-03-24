<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class CurrencyPairListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, CurrencyPairResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
