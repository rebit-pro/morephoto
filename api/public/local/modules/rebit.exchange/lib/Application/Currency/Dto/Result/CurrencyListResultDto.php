<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class CurrencyListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, CurrencyResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
