<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class CurrencyPairResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public string $code,
        public int $tokenCurrencyId,
        public int $fiatCurrencyId,
        public bool $isDefault,
        public int $sort,
    ) {}
}
