<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Dto\Request;

/**
 * DTO для блокировки средств под сделку.
 * Используется внутренне модулем Exchange при создании сделки.
 */
final readonly class LockFundsDto
{
    public function __construct(
        public int $userId,
        public int $currencyId,
        public float $amount,
        public ?int $tradeId = null,
    ) {}
}
