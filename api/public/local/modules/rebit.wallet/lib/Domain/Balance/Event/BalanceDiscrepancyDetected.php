<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Event;

final readonly class BalanceDiscrepancyDetected
{
    public function __construct(
        public int $userId,
        public int $currencyId,
        public float $localTotal,
        public float $bybitTotal,
        public float $difference,
    ) {}
}
