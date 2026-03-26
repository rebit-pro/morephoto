<?php
declare(strict_types=1);
namespace Rebit\Wallet\Tests\Application\Balance\UseCase;
use PHPUnit\Framework\TestCase;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Wallet\Application\Balance\Dto\Request\LockFundsInputDto;
use Rebit\Wallet\Application\Balance\UseCase\UnlockFundsUseCase;
use Rebit\Wallet\Domain\Balance\Entity\Balance;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
/**
 * @internal
 */
final class UnlockFundsUseCaseTest extends TestCase
{
    private const int USER_ID = 1;
    private const int CURRENCY_ID = 10;
    public function testSuccessfulUnlock(): void
    {
        $dto = new LockFundsInputDto(
            userId: self::USER_ID,
            currencyId: self::CURRENCY_ID,
            amount: 3.0,
            tradeId: 200,
        );
        $balance = $this->createStub(Balance::class);
        $balance->method('getUfLocked')->willReturn(10.0);
        $balance->method('getUfAvailable')->willReturn(5.0);
        $balanceRepo = $this->createMock(BalanceRepository::class);
        $balanceRepo
            ->expects(self::once())
            ->method('findByUserIdAndCurrencyId')
            ->with(self::USER_ID, self::CURRENCY_ID)
            ->willReturn($balance);
        $balanceRepo
            ->expects(self::once())
            ->method('unlockFunds')
            ->with($balance, 3.0);
        $transactionRepo = $this->createMock(TransactionRepository::class);
        $transactionRepo
            ->expects(self::once())
            ->method('create')
            ->with(
                userId: self::USER_ID,
                currencyId: self::CURRENCY_ID,
                type: TransactionTypeEnum::Unlock,
                amount: 3.0,
                balanceAfter: 8.0,
                tradeId: 200,
                description: 'Разблокировка средств после отмены сделки',
            );
        $calculator = new BalanceCalculator();
        (new UnlockFundsUseCase($balanceRepo, $transactionRepo, $calculator))->execute($dto);
    }
    public function testThrows404WhenBalanceNotFound(): void
    {
        $dto = new LockFundsInputDto(
            userId: self::USER_ID,
            currencyId: self::CURRENCY_ID,
            amount: 3.0,
        );
        $balanceRepo = $this->createStub(BalanceRepository::class);
        $balanceRepo->method('findByUserIdAndCurrencyId')->willReturn(null);
        $transactionRepo = $this->createStub(TransactionRepository::class);
        $calculator = new BalanceCalculator();
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        (new UnlockFundsUseCase($balanceRepo, $transactionRepo, $calculator))->execute($dto);
    }
    public function testThrows422WhenInsufficientLocked(): void
    {
        $dto = new LockFundsInputDto(
            userId: self::USER_ID,
            currencyId: self::CURRENCY_ID,
            amount: 50.0,
        );
        $balance = $this->createStub(Balance::class);
        $balance->method('getUfLocked')->willReturn(3.0);
        $balanceRepo = $this->createStub(BalanceRepository::class);
        $balanceRepo->method('findByUserIdAndCurrencyId')->willReturn($balance);
        $transactionRepo = $this->createStub(TransactionRepository::class);
        $calculator = new BalanceCalculator();
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);
        (new UnlockFundsUseCase($balanceRepo, $transactionRepo, $calculator))->execute($dto);
    }
}
