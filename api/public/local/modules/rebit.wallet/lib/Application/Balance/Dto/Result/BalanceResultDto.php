<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class BalanceResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $currencyId,
        public string $currency,
        public float $available,
        public float $locked,
        public float $total,
        public ?string $syncedAt = null,
    ) {}
}
