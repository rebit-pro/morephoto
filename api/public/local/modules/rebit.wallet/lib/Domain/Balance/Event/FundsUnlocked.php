<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Event;

final readonly class FundsUnlocked
{
    public function __construct(
        public int $userId,
        public int $currencyId,
        public float $amount,
        public ?int $tradeId = null,
    ) {}
}
