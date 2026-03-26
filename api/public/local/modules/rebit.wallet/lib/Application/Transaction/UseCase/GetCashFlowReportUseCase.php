<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\UseCase;

use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Transaction\Dto\Request\CashFlowFilterRequestDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\CashFlowItemResultDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\CashFlowReportResultDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\CashFlowTotalsResultDto;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;

/**
 * Формирование бухгалтерско-управленческого отчёта по оборотам денежных средств.
 *
 * Формат: остаток на начало периода, приход, расход, остаток на конец — в разрезе валют.
 */
final readonly class GetCashFlowReportUseCase
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private CurrencyQueryInterface $currencyQuery,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId, CashFlowFilterRequestDto $filter): CashFlowReportResultDto
    {
        $dateFrom = $filter->dateFrom;
        $dateTo = $filter->dateTo;

        // 1. Получаем обороты за период (приход/расход) сгруппированные по валюте
        $turnover = $this->transactionRepository->sumTurnoverByCurrency(
            $userId,
            $dateFrom,
            $dateTo,
            $filter->currencyId,
        );

        // 2. Получаем остатки на начало периода (balance_after из последней транзакции до dateFrom)
        $openingBalances = [];
        if (null !== $dateFrom && '' !== $dateFrom) {
            $openingBalances = $this->transactionRepository->getBalanceBeforeDate(
                $userId,
                $dateFrom,
                $filter->currencyId,
            );
        }

        // 3. Собираем все ID валют
        $currencyIds = array_unique(
            array_merge(array_keys($turnover), array_keys($openingBalances)),
        );

        if (null !== $filter->currencyId && !in_array($filter->currencyId, $currencyIds, true)) {
            $currencyIds[] = $filter->currencyId;
        }

        sort($currencyIds);

        // 4. Формируем строки отчёта
        $items = [];
        $totalIncoming = 0.0;
        $totalOutgoing = 0.0;
        $totalOpening = 0.0;
        $totalClosing = 0.0;

        foreach ($currencyIds as $cid) {
            $opening = $openingBalances[$cid] ?? 0.0;
            $incoming = $turnover[$cid]['incoming'] ?? 0.0;
            $outgoing = $turnover[$cid]['outgoing'] ?? 0.0;
            $closing = $opening + $incoming - $outgoing;

            $currencyCode = $this->currencyQuery->findCodeById($cid) ?? "CUR_{$cid}";

            $items[] = new CashFlowItemResultDto(
                currencyId: $cid,
                currency: $currencyCode,
                openingBalance: round($opening, 8),
                incoming: round($incoming, 8),
                outgoing: round($outgoing, 8),
                closingBalance: round($closing, 8),
            );

            $totalOpening += $opening;
            $totalIncoming += $incoming;
            $totalOutgoing += $outgoing;
            $totalClosing += $closing;
        }

        return new CashFlowReportResultDto(
            items: $items,
            totals: new CashFlowTotalsResultDto(
                totalIncoming: round($totalIncoming, 8),
                totalOutgoing: round($totalOutgoing, 8),
                totalOpeningBalance: round($totalOpening, 8),
                totalClosingBalance: round($totalClosing, 8),
            ),
        );
    }
}
