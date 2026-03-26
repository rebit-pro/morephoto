<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\UseCase;

use Rebit\Wallet\Application\Transaction\Dto\Request\TransactionFilterRequestDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\TransactionListResultDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\TransactionResultDto;
use Rebit\Wallet\Domain\Transaction\Entity\Transaction;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;

final readonly class ListTransactionsUseCase
{
    public function __construct(
        private TransactionRepository $transactionRepository,
    ) {}

    public function execute(int $userId, TransactionFilterRequestDto $filter): TransactionListResultDto
    {
        $collection = $this->transactionRepository->findByFilter($userId, $filter);
        $total = $this->transactionRepository->countByFilter($userId, $filter);

        $transactions = array_map(
            static fn(Transaction $transaction): TransactionResultDto => new TransactionResultDto(
                id: (int)$transaction->getId(),
                userId: (int)$transaction->getUfUserId(),
                currencyId: (int)$transaction->getUfCurrencyId(),
                type: TransactionTypeEnum::from((string)$transaction->getUfType()),
                amount: (float)$transaction->getUfAmount(),
                balanceAfter: (float)$transaction->getUfBalanceAfter(),
                tradeId: $transaction->getUfTradeId(),
                description: $transaction->getUfDescription(),
                bybitTxId: $transaction->getUfBybitTxId(),
                createdAt: $transaction->getUfCreatedAt()?->format('c') ?? '',
            ),
            $collection->getAll(),
        );

        return new TransactionListResultDto($transactions, $total);
    }
}
