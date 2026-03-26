<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\UseCase;

use Rebit\Wallet\Application\Balance\Dto\Request\LockFundsInputDto;
use Rebit\Wallet\Domain\Balance\Exception\InsufficientLockedFundsException;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Разблокировка средств после отмены сделки.
 * Вызывается модулем Exchange при отмене сделки.
 */
final readonly class UnlockFundsUseCase
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
    public function execute(LockFundsInputDto $dto): void
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

        try {
            $this->balanceCalculator->assertCanUnlock($balance->getUfLocked(), $dto->amount);
        } catch (InsufficientLockedFundsException $e) {
            throw new HttpException($e->getMessage(), 422, $e);
        }

        $this->balanceRepository->unlockFunds($balance, $dto->amount);

        $this->transactionRepository->create(
            userId: $dto->userId,
            currencyId: $dto->currencyId,
            type: TransactionTypeEnum::Unlock,
            amount: $dto->amount,
            balanceAfter: $balance->getUfAvailable() + $dto->amount,
            tradeId: $dto->tradeId,
            description: 'Разблокировка средств после отмены сделки',
        );
    }
}
