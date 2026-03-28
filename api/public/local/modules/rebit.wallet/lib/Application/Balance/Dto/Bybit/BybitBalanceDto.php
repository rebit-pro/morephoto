<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Dto\Bybit;

final readonly class BybitBalanceDto
{
    public function __construct(
        public string $coin,
        public float $available,
        public float $locked,
        public float $total,
    ) {}
}
