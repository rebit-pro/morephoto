<?php
declare(strict_types=1);
namespace Rebit\Wallet\Tests\Application\Balance\UseCase;
use PHPUnit\Framework\TestCase;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Wallet\Application\Balance\Dto\Request\LockFundsInputDto;
use Rebit\Wallet\Application\Balance\UseCase\LockFundsUseCase;
use Rebit\Wallet\Domain\Balance\Entity\Balance;
use Rebit\Wallet\Domain\Balance\Exception\InsufficientFundsException;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
/**
 * @internal
 */
final class LockFundsUseCaseTest extends TestCase
{
    private const int USER_ID = 1;
    private const int CURRENCY_ID = 10;
    public function testSuccessfulLock(): void
    {
        $dto = new LockFundsInputDto(
            userId: self::USER_ID,
            currencyId: self::CURRENCY_ID,
            amount: 5.0,
            tradeId: 100,
        );
        $balance = $this->createStub(Balance::class);
        $balance->method('getUfAvailable')->willReturn(10.0);
        $balanceRepo = $this->createMock(BalanceRepository::class);
        $balanceRepo
            ->expects(self::once())
            ->method('findByUserIdAndCurrencyId')
            ->with(self::USER_ID, self::CURRENCY_ID)
            ->willReturn($balance);
        $balanceRepo
            ->expects(self::once())
            ->method('lockFunds')
            ->with($balance, 5.0);
        $transactionRepo = $this->createMock(TransactionRepository::class);
        $transactionRepo
            ->expects(self::once())
            ->method('create')
            ->with(
                userId: self::USER_ID,
                currencyId: self::CURRENCY_ID,
                type: TransactionTypeEnum::Lock,
                amount: -5.0,
                balanceAfter: 5.0,
                tradeId: 100,
                description: 'Блокировка средств под сделку',
            );
        $calculator = new BalanceCalculator();
        (new LockFundsUseCase($balanceRepo, $transactionRepo, $calculator))->execute($dto);
    }
    public function testThrows404WhenBalanceNotFound(): void
    {
        $dto = new LockFundsInputDto(
            userId: self::USER_ID,
            currencyId: self::CURRENCY_ID,
            amount: 5.0,
        );
        $balanceRepo = $this->createStub(BalanceRepository::class);
        $balanceRepo->method('findByUserIdAndCurrencyId')->willReturn(null);
        $transactionRepo = $this->createStub(TransactionRepository::class);
        $calculator = new BalanceCalculator();
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        (new LockFundsUseCase($balanceRepo, $transactionRepo, $calculator))->execute($dto);
    }
    public function testThrows422WhenInsufficientFunds(): void
    {
        $dto = new LockFundsInputDto(
            userId: self::USER_ID,
            currencyId: self::CURRENCY_ID,
            amount: 100.0,
        );
        $balance = $this->createStub(Balance::class);
        $balance->method('getUfAvailable')->willReturn(5.0);
        $balanceRepo = $this->createStub(BalanceRepository::class);
        $balanceRepo->method('findByUserIdAndCurrencyId')->willReturn($balance);
        $transactionRepo = $this->createStub(TransactionRepository::class);
        $calculator = new BalanceCalculator();
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);
        (new LockFundsUseCase($balanceRepo, $transactionRepo, $calculator))->execute($dto);
    }
}
