<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Dto\Request;

/**
 * DTO для блокировки/разблокировки средств под сделку.
 * Используется внутренне модулем Exchange при создании/отмене сделки.
 */
final readonly class LockFundsInputDto
{
    public function __construct(
        public int $userId,
        public int $currencyId,
        public float $amount,
        public ?int $tradeId = null,
    ) {}
}
