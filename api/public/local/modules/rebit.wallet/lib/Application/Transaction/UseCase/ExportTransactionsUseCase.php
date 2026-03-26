<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\UseCase;

use Rebit\Wallet\Application\Transaction\Dto\Request\TransactionFilterRequestDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\TransactionListResultDto;

/**
 * Экспорт транзакций.
 *
 * Использует ListTransactionsUseCase для получения данных.
 * Возвращает TransactionListResultDto — формат экспорта (CSV/Excel)
 * определяется на уровне контроллера или инфраструктуры.
 *
 * @todo Реализовать экспорт в CSV/Excel через PhpSpreadsheet.
 */
final readonly class ExportTransactionsUseCase
{
    public function __construct(
        private ListTransactionsUseCase $listTransactionsUseCase,
    ) {}

    public function execute(int $userId, TransactionFilterRequestDto $filter): TransactionListResultDto
    {
        $exportFilter = new TransactionFilterRequestDto(
            type: $filter->type,
            currencyId: $filter->currencyId,
            dateFrom: $filter->dateFrom,
            dateTo: $filter->dateTo,
            limit: 10000,
            offset: 0,
        );

        return $this->listTransactionsUseCase->execute($userId, $exportFilter);
    }
}
