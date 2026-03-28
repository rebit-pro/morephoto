<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\OrderBook\UseCase;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\OrderBook\Dto\Bybit\BybitOrderBookItemDto;
use Rebit\Exchange\Application\OrderBook\Dto\Bybit\BybitOrderBookListDto;
use Rebit\Exchange\Application\OrderBook\Port\BybitOrderBookGatewayInterface;
use Rebit\Exchange\Application\OrderBook\UseCase\SyncOrderBookUseCase;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class SyncOrderBookUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testSyncsAllActivePairsForBothSides(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(1);
        $pair->method('getUfCode')->willReturn('USDT_RUB');
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$pair]));
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findActive')->willReturn($collection);
        $gateway = $this->createMock(BybitOrderBookGatewayInterface::class);
        $gateway
            ->expects($this->exactly(2))
            ->method('fetchOrderBook')
            ->willReturnCallback(function(int $userId, string $tokenId, string $currencyId, string $side): BybitOrderBookListDto {
                self::assertSame(self::USER_ID, $userId);
                self::assertSame('USDT', $tokenId);
                self::assertSame('RUB', $currencyId);

                return new BybitOrderBookListDto(items: [
                    new BybitOrderBookItemDto(
                        id: 'bybit-1',
                        price: '95.5',
                        lastQuantity: '100',
                        minAmount: '1000',
                        maxAmount: '50000',
                        nickName: 'Trader1',
                        recentExecuteRate: 0.98,
                        recentOrderNum: 150,
                        payments: ['pm_1'],
                        paymentPeriod: 15,
                        side: (int)$side,
                    ),
                ]);
            })
        ;
        $orderBookRepo = $this->createMock(OrderBookRepository::class);
        $orderBookRepo
            ->expects($this->exactly(2))
            ->method('replaceByCurrencyPairAndSide')
        ;
        $logger = $this->createStub(LoggerInterface::class);
        (new SyncOrderBookUseCase($orderBookRepo, $pairRepo, $gateway, $logger))
            ->execute(self::USER_ID)
        ;
    }

    public function testSkipsInvalidPairCodeFormat(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(1);
        $pair->method('getUfCode')->willReturn('INVALID');
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$pair]));
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findActive')->willReturn($collection);
        $gateway = $this->createMock(BybitOrderBookGatewayInterface::class);
        $gateway->expects($this->never())->method('fetchOrderBook');
        $orderBookRepo = $this->createMock(OrderBookRepository::class);
        $orderBookRepo->expects($this->never())->method('replaceByCurrencyPairAndSide');
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->exactly(2))
            ->method('warning')
            ->with('Invalid pair code format', $this->anything())
        ;
        (new SyncOrderBookUseCase($orderBookRepo, $pairRepo, $gateway, $logger))
            ->execute(self::USER_ID)
        ;
    }

    public function testBybitErrorLogsWarningAndContinues(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(1);
        $pair->method('getUfCode')->willReturn('USDT_RUB');
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$pair]));
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findActive')->willReturn($collection);
        $gateway = $this->createStub(BybitOrderBookGatewayInterface::class);
        $gateway->method('fetchOrderBook')->willThrowException(new HttpException('API error'));
        $orderBookRepo = $this->createMock(OrderBookRepository::class);
        $orderBookRepo->expects($this->never())->method('replaceByCurrencyPairAndSide');
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->exactly(2))
            ->method('warning')
            ->with('OrderBook sync failed', $this->anything())
        ;
        (new SyncOrderBookUseCase($orderBookRepo, $pairRepo, $gateway, $logger))
            ->execute(self::USER_ID)
        ;
    }

    public function testEmptyOrderBookFromBybit(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(1);
        $pair->method('getUfCode')->willReturn('USDT_RUB');
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$pair]));
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findActive')->willReturn($collection);
        $gateway = $this->createStub(BybitOrderBookGatewayInterface::class);
        $gateway->method('fetchOrderBook')->willReturn(new BybitOrderBookListDto(items: []));
        $orderBookRepo = $this->createMock(OrderBookRepository::class);
        $orderBookRepo
            ->expects($this->exactly(2))
            ->method('replaceByCurrencyPairAndSide')
            ->willReturnCallback(function(int $pairId, string $side, array $entries): void {
                self::assertSame(1, $pairId);
                self::assertSame([], $entries);
            })
        ;
        $logger = $this->createStub(LoggerInterface::class);
        (new SyncOrderBookUseCase($orderBookRepo, $pairRepo, $gateway, $logger))
            ->execute(self::USER_ID)
        ;
    }

    public function testNoPairsDoesNothing(): void
    {
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findActive')->willReturn($collection);
        $gateway = $this->createMock(BybitOrderBookGatewayInterface::class);
        $gateway->expects($this->never())->method('fetchOrderBook');
        $orderBookRepo = $this->createMock(OrderBookRepository::class);
        $orderBookRepo->expects($this->never())->method('replaceByCurrencyPairAndSide');
        $logger = $this->createStub(LoggerInterface::class);
        (new SyncOrderBookUseCase($orderBookRepo, $pairRepo, $gateway, $logger))
            ->execute(self::USER_ID)
        ;
    }

    public function testMapsApiResponseFieldsCorrectly(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(5);
        $pair->method('getUfCode')->willReturn('BTC_RUB');
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$pair]));
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findActive')->willReturn($collection);
        $gateway = $this->createStub(BybitOrderBookGatewayInterface::class);
        $gateway->method('fetchOrderBook')->willReturn(new BybitOrderBookListDto(items: [
            new BybitOrderBookItemDto(
                id: 'bybit-42',
                price: '100.5',
                lastQuantity: '10',
                minAmount: '500',
                maxAmount: '20000',
                nickName: 'TopTrader',
                recentExecuteRate: 0.99,
                recentOrderNum: 300,
                payments: ['pm_bank', 'pm_card'],
                paymentPeriod: 30,
                side: 0,
            ),
        ]));
        $capturedEntries = [];
        $orderBookRepo = $this->createMock(OrderBookRepository::class);
        $orderBookRepo
            ->expects($this->exactly(2))
            ->method('replaceByCurrencyPairAndSide')
            ->willReturnCallback(function(int $pairId, string $side, array $entries) use (&$capturedEntries): void {
                if ([] !== $entries) {
                    $capturedEntries = $entries;
                }
            })
        ;
        $logger = $this->createStub(LoggerInterface::class);
        (new SyncOrderBookUseCase($orderBookRepo, $pairRepo, $gateway, $logger))
            ->execute(self::USER_ID)
        ;
        self::assertCount(1, $capturedEntries);
        $entry = $capturedEntries[0];
        self::assertSame('bybit-42', $entry['bybitOrderId']);
        self::assertSame(5, $entry['currencyPairId']);
        self::assertSame(100.5, $entry['price']);
        self::assertSame(10.0, $entry['quantity']);
        self::assertSame(500.0, $entry['minAmount']);
        self::assertSame(20000.0, $entry['maxAmount']);
        self::assertSame('TopTrader', $entry['counterpartyName']);
        self::assertSame(300, $entry['counterpartyTrades']);
        self::assertSame(0.99, $entry['counterpartyCompletionRate']);
        self::assertSame('["pm_bank","pm_card"]', $entry['paymentMethodIds']);
        self::assertSame(30, $entry['paymentTimeLimit']);
    }
}
