<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\UseCase;

use Rebit\Wallet\Domain\Balance\Dto\Request\LockFundsDto;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Блокировка средств под сделку.
 * Вызывается модулем Exchange при создании сделки.
 */
final readonly class LockFundsUseCase
{
    public function __construct(
        private BalanceRepository $balanceRepository,
        private TransactionRepository $transactionRepository,
        private BalanceCalculator $balanceCalculator,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(LockFundsDto $dto): void
    {
        $balance = $this->balanceRepository->findByUserIdAndCurrencyId(
            $dto->userId,
            $dto->currencyId,
        );

        if (null === $balance) {
            throw new HttpException(
                sprintf('Баланс не найден: userId=%d, currencyId=%d', $dto->userId, $dto->currencyId),
                404,
            );
        }

        $this->balanceCalculator->assertCanLock($balance->getUfAvailable(), $dto->amount);

        $this->balanceRepository->lockFunds($balance, $dto->amount);

        $this->transactionRepository->create(
            userId: $dto->userId,
            currencyId: $dto->currencyId,
            type: TransactionTypeEnum::Lock,
            amount: -$dto->amount,
            balanceAfter: $balance->getUfAvailable() - $dto->amount,
            tradeId: $dto->tradeId,
            description: 'Блокировка средств под сделку',
        );
    }
}
