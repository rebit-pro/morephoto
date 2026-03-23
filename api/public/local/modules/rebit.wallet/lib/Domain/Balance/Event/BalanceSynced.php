<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Event;

final readonly class BalanceSynced
{
    public function __construct(
        public int $userId,
        public int $currencyId,
        public float $available,
        public float $locked,
        public float $total,
    ) {}
}
