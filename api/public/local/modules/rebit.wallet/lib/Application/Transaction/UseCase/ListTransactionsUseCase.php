<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\UseCase;

use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Transaction\Dto\Request\TransactionFilterRequestDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\TransactionListResultDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\TransactionResultDto;
use Rebit\Wallet\Domain\Transaction\Entity\Transaction;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Wallet\Domain\Transaction\ValueObject\TransactionFilter;

final readonly class ListTransactionsUseCase
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private CurrencyQueryInterface $currencyQuery,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId, TransactionFilterRequestDto $filter): TransactionListResultDto
    {
        $domainFilter = new TransactionFilter(
            type: $filter->type,
            currencyId: $filter->currencyId,
            dateFrom: $filter->dateFrom,
            dateTo: $filter->dateTo,
            limit: $filter->limit,
            offset: $filter->offset,
        );

        $collection = $this->transactionRepository->findByFilter($userId, $domainFilter);
        $total = $this->transactionRepository->countByFilter($userId, $domainFilter);

        $transactions = array_map(
            fn(Transaction $transaction): TransactionResultDto => new TransactionResultDto(
                id: (int)$transaction->getId(),
                userId: (int)$transaction->getUfUserId(),
                currencyId: (int)$transaction->getUfCurrencyId(),
                currency: $this->currencyQuery->findCodeById((int)$transaction->getUfCurrencyId()) ?? "CUR_{$transaction->getUfCurrencyId()}",
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
