<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\UseCase;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Application\Trade\UseCase\SyncTradeHistoryUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;

/**
 * @internal
 */
final class SyncTradeHistoryUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testCreatesNewTradesFromBybit(): void
    {
        $gateway = $this->createMock(BybitTradeGatewayInterface::class);
        $gateway
            ->expects($this->exactly(2))
            ->method('fetchAllOrders')
            ->willReturnCallback(static fn(int $userId, int $page): array => match ($page) {
                1 => ['items' => [
                    ['id' => 'bybit-100', 'status' => 50, 'side' => 0, 'price' => '95.5', 'amount' => '1000', 'fee' => '0.5', 'targetNickName' => 'Bob'],
                    ['id' => 'bybit-101', 'status' => 40, 'side' => 1, 'price' => '96.0', 'amount' => '2000', 'fee' => '1.0', 'targetNickName' => 'Alice'],
                ]],
                default => ['items' => []],
            })
        ;

        $tradeRepo = $this->createMock(TradeRepository::class);
        $tradeRepo->method('findByBybitOrderId')->willReturn(null);
        $tradeRepo
            ->expects($this->exactly(2))
            ->method('createFromBybit')
        ;

        $useCase = new SyncTradeHistoryUseCase($tradeRepo, $gateway, new NullLogger());

        [$new, $updated] = $useCase->execute(self::USER_ID);

        self::assertSame(2, $new);
        self::assertSame(0, $updated);
    }

    public function testUpdatesExistingTradeStatus(): void
    {
        $gateway = $this->createStub(BybitTradeGatewayInterface::class);
        $gateway->method('fetchAllOrders')->willReturnCallback(
            static fn(int $userId, int $page): array => match ($page) {
                1 => ['items' => [
                    ['id' => 'bybit-200', 'status' => 50, 'side' => 0, 'price' => '95', 'amount' => '500', 'fee' => '0', 'targetNickName' => 'Eve'],
                ]],
                default => ['items' => []],
            },
        );

        $trade = $this->createMock(Trade::class);
        $trade->method('getUfBybitStatus')->willReturn(10);
        $trade->expects($this->once())->method('setUfBybitStatus')->with(50);
        $trade->expects($this->once())->method('setUfStatus')->with('completed');

        $tradeRepo = $this->createMock(TradeRepository::class);
        $tradeRepo->method('findByBybitOrderId')->willReturn($trade);
        $tradeRepo->expects($this->once())->method('save')->with($trade);
        $tradeRepo->expects($this->never())->method('createFromBybit');

        $useCase = new SyncTradeHistoryUseCase($tradeRepo, $gateway, new NullLogger());

        [$new, $updated] = $useCase->execute(self::USER_ID);

        self::assertSame(0, $new);
        self::assertSame(1, $updated);
    }

    public function testSkipsExistingTradeWithSameStatus(): void
    {
        $gateway = $this->createStub(BybitTradeGatewayInterface::class);
        $gateway->method('fetchAllOrders')->willReturnCallback(
            static fn(int $userId, int $page): array => match ($page) {
                1 => ['items' => [
                    ['id' => 'bybit-300', 'status' => 50, 'side' => 0, 'price' => '95', 'amount' => '500', 'fee' => '0', 'targetNickName' => 'Eve'],
                ]],
                default => ['items' => []],
            },
        );

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBybitStatus')->willReturn(50);

        $tradeRepo = $this->createMock(TradeRepository::class);
        $tradeRepo->method('findByBybitOrderId')->willReturn($trade);
        $tradeRepo->expects($this->never())->method('save');
        $tradeRepo->expects($this->never())->method('createFromBybit');

        $useCase = new SyncTradeHistoryUseCase($tradeRepo, $gateway, new NullLogger());

        [$new, $updated] = $useCase->execute(self::USER_ID);

        self::assertSame(0, $new);
        self::assertSame(0, $updated);
    }

    public function testSkipsItemsWithEmptyId(): void
    {
        $gateway = $this->createStub(BybitTradeGatewayInterface::class);
        $gateway->method('fetchAllOrders')->willReturnCallback(
            static fn(int $userId, int $page): array => match ($page) {
                1 => ['items' => [
                    ['id' => '', 'status' => 50],
                    ['status' => 50],
                ]],
                default => ['items' => []],
            },
        );

        $tradeRepo = $this->createMock(TradeRepository::class);
        $tradeRepo->expects($this->never())->method('findByBybitOrderId');
        $tradeRepo->expects($this->never())->method('createFromBybit');

        $useCase = new SyncTradeHistoryUseCase($tradeRepo, $gateway, new NullLogger());

        [$new, $updated] = $useCase->execute(self::USER_ID);

        self::assertSame(0, $new);
        self::assertSame(0, $updated);
    }

    public function testPaginatesUntilEmpty(): void
    {
        $callCount = 0;
        $gateway = $this->createMock(BybitTradeGatewayInterface::class);
        $gateway
            ->expects($this->exactly(3))
            ->method('fetchAllOrders')
            ->willReturnCallback(static function(int $userId, int $page) use (&$callCount): array {
                ++$callCount;

                return match ($page) {
                    1 => ['items' => [['id' => 'a', 'status' => 50, 'side' => 0, 'price' => '1', 'amount' => '1', 'fee' => '0', 'targetNickName' => '']]],
                    2 => ['items' => [['id' => 'b', 'status' => 50, 'side' => 0, 'price' => '1', 'amount' => '1', 'fee' => '0', 'targetNickName' => '']]],
                    default => ['items' => []],
                };
            })
        ;

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findByBybitOrderId')->willReturn(null);

        $useCase = new SyncTradeHistoryUseCase($tradeRepo, $gateway, new NullLogger());

        [$new] = $useCase->execute(self::USER_ID);

        self::assertSame(2, $new);
    }
}
