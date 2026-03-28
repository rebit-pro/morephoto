<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\UseCase;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderListDto;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderSummaryDto;
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
            ->willReturnCallback(static fn(int $userId, int $page): BybitTradeOrderListDto => match ($page) {
                1 => new BybitTradeOrderListDto(count: 2, items: [
                    new BybitTradeOrderSummaryDto('bybit-100', 0, '1000', '95.5', '0.5', 'Bob', '', 50, '', ''),
                    new BybitTradeOrderSummaryDto('bybit-101', 1, '2000', '96.0', '1.0', 'Alice', '', 40, '', ''),
                ]),
                default => new BybitTradeOrderListDto(count: 0, items: []),
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
            static fn(int $userId, int $page): BybitTradeOrderListDto => match ($page) {
                1 => new BybitTradeOrderListDto(count: 1, items: [
                    new BybitTradeOrderSummaryDto('bybit-200', 0, '500', '95', '0', 'Eve', '', 50, '', ''),
                ]),
                default => new BybitTradeOrderListDto(count: 0, items: []),
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
            static fn(int $userId, int $page): BybitTradeOrderListDto => match ($page) {
                1 => new BybitTradeOrderListDto(count: 1, items: [
                    new BybitTradeOrderSummaryDto('bybit-300', 0, '500', '95', '0', 'Eve', '', 50, '', ''),
                ]),
                default => new BybitTradeOrderListDto(count: 0, items: []),
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
            static fn(int $userId, int $page): BybitTradeOrderListDto => match ($page) {
                1 => new BybitTradeOrderListDto(count: 2, items: [
                    new BybitTradeOrderSummaryDto('', 0, '', '', '', '', '', 50, '', ''),
                    new BybitTradeOrderSummaryDto('', 0, '', '', '', '', '', 50, '', ''),
                ]),
                default => new BybitTradeOrderListDto(count: 0, items: []),
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
            ->willReturnCallback(static function(int $userId, int $page) use (&$callCount): BybitTradeOrderListDto {
                ++$callCount;

                return match ($page) {
                    1 => new BybitTradeOrderListDto(count: 1, items: [
                        new BybitTradeOrderSummaryDto('a', 0, '1', '1', '0', '', '', 50, '', ''),
                    ]),
                    2 => new BybitTradeOrderListDto(count: 1, items: [
                        new BybitTradeOrderSummaryDto('b', 0, '1', '1', '0', '', '', 50, '', ''),
                    ]),
                    default => new BybitTradeOrderListDto(count: 0, items: []),
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
