<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

/**
 * Итоговые суммы отчёта по оборотам (агрегат по всем валютам).
 */
final readonly class CashFlowTotalsResultDto implements ResultDtoInterface
{
    public function __construct(
        public float $totalIncoming,
        public float $totalOutgoing,
        public float $totalOpeningBalance,
        public float $totalClosingBalance,
    ) {}
}
