<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class CurrencyResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public string $type,
        public int $decimals,
        public int $sort,
    ) {}
}
