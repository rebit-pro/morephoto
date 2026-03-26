<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

/**
 * Результат отчёта по оборотам денежных средств.
 *
 * Формат: остаток на начало, приход, расход, остаток на конец — в разрезе валют.
 */
final readonly class CashFlowReportResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, CashFlowItemResultDto> $items
     */
    public function __construct(
        public array $items,
        public ?CashFlowTotalsResultDto $totals,
    ) {}
}
