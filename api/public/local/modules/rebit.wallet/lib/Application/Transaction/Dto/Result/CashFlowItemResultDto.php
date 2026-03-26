<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

/**
 * Строка отчёта по оборотам для одной валюты.
 */
final readonly class CashFlowItemResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $currencyId,
        public string $currency,
        public float $openingBalance,
        public float $incoming,
        public float $outgoing,
        public float $closingBalance,
    ) {}
}
