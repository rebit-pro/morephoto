<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Wallet\Domain\Transaction\Dto\Request\TransactionFilterDto;
use Rebit\Wallet\Domain\Transaction\Dto\Result\TransactionListResultDto;
use Rebit\Wallet\Domain\Transaction\Dto\Result\TransactionResultDto;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;

final readonly class ListTransactionsUseCase
{
    public function __construct(
        private TransactionRepository $transactionRepository,
    ) {}

    public function execute(int $userId, TransactionFilterDto $filter): TransactionListResultDto
    {
        $rows = $this->transactionRepository->findByFilter($userId, $filter);
        $total = $this->transactionRepository->countByFilter($userId, $filter);

        $transactions = array_map(
            static fn(array $row): TransactionResultDto => new TransactionResultDto(
                id: (int)$row['ID'],
                userId: (int)$row['UF_USER_ID'],
                currencyId: (int)$row['UF_CURRENCY_ID'],
                type: TransactionTypeEnum::from($row['UF_TYPE']),
                amount: (float)$row['UF_AMOUNT'],
                balanceAfter: (float)$row['UF_BALANCE_AFTER'],
                tradeId: null !== $row['UF_TRADE_ID'] ? (int)$row['UF_TRADE_ID'] : null,
                description: $row['UF_DESCRIPTION'] ?? null,
                bybitTxId: $row['UF_BYBIT_TX_ID'] ?? null,
                createdAt: $row['UF_CREATED_AT'] instanceof DateTime
                    ? $row['UF_CREATED_AT']->format('c')
                    : (string)$row['UF_CREATED_AT'],
            ),
            $rows,
        );

        return new TransactionListResultDto($transactions, $total);
    }
}
