<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\OrderBook\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookListResultDto;
use Rebit\Exchange\Application\OrderBook\UseCase\GetOrderBookUseCase;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;

/**
 * @internal
 */
final class GetOrderBookUseCaseTest extends TestCase
{
    public function testReturnsEmptyListWhenNoEntries(): void
    {
        $collection = $this->createStub(OrderBookEntryCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $repo = $this->createMock(OrderBookRepository::class);
        $repo
            ->expects($this->once())
            ->method('findByCurrencyPairAndSide')
            ->with(1, 'buy')
            ->willReturn($collection)
        ;
        $result = (new GetOrderBookUseCase($repo))->execute(1, 'buy');
        self::assertInstanceOf(OrderBookListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithEntries(): void
    {
        $entry = $this->createStub(OrderBookEntry::class);
        $entry->method('getId')->willReturn(1);
        $entry->method('getUfBybitOrderId')->willReturn('order-123');
        $entry->method('getUfSide')->willReturn('buy');
        $entry->method('getUfPrice')->willReturn(95.5);
        $entry->method('getUfQuantity')->willReturn(100.0);
        $entry->method('getUfMinAmount')->willReturn(1000.0);
        $entry->method('getUfMaxAmount')->willReturn(50000.0);
        $entry->method('getUfCounterpartyName')->willReturn('Trader1');
        $entry->method('getUfCounterpartyRating')->willReturn(4.8);
        $entry->method('getUfCounterpartyTrades')->willReturn(150);
        $entry->method('getUfCounterpartyCompletionRate')->willReturn(0.98);
        $entry->method('getUfPaymentMethodIds')->willReturn('["pm_1","pm_2"]');
        $entry->method('getUfPaymentTimeLimit')->willReturn(15);
        $collection = $this->createStub(OrderBookEntryCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$entry]));
        $repo = $this->createStub(OrderBookRepository::class);
        $repo->method('findByCurrencyPairAndSide')->willReturn($collection);
        $result = (new GetOrderBookUseCase($repo))->execute(1, 'buy');
        self::assertCount(1, $result->items);
        $item = $result->items[0];
        self::assertSame(1, $item->id);
        self::assertSame('order-123', $item->bybitOrderId);
        self::assertSame('buy', $item->side);
        self::assertSame(95.5, $item->price);
        self::assertSame(100.0, $item->quantity);
        self::assertSame(1000.0, $item->minAmount);
        self::assertSame(50000.0, $item->maxAmount);
        self::assertSame('Trader1', $item->counterpartyName);
        self::assertSame(4.8, $item->counterpartyRating);
        self::assertSame(150, $item->counterpartyTrades);
        self::assertSame(0.98, $item->counterpartyCompletionRate);
        self::assertSame(['pm_1', 'pm_2'], $item->paymentMethodIds);
        self::assertSame(15, $item->paymentTimeLimit);
    }

    public function testEmptyPaymentMethodIdsDecodedAsEmptyArray(): void
    {
        $entry = $this->createStub(OrderBookEntry::class);
        $entry->method('getId')->willReturn(2);
        $entry->method('getUfBybitOrderId')->willReturn('order-456');
        $entry->method('getUfSide')->willReturn('sell');
        $entry->method('getUfPrice')->willReturn(96.0);
        $entry->method('getUfQuantity')->willReturn(50.0);
        $entry->method('getUfMinAmount')->willReturn(500.0);
        $entry->method('getUfMaxAmount')->willReturn(10000.0);
        $entry->method('getUfCounterpartyName')->willReturn('Trader2');
        $entry->method('getUfCounterpartyRating')->willReturn(0.0);
        $entry->method('getUfCounterpartyTrades')->willReturn(0);
        $entry->method('getUfCounterpartyCompletionRate')->willReturn(0.0);
        $entry->method('getUfPaymentMethodIds')->willReturn('');
        $entry->method('getUfPaymentTimeLimit')->willReturn(30);
        $collection = $this->createStub(OrderBookEntryCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$entry]));
        $repo = $this->createStub(OrderBookRepository::class);
        $repo->method('findByCurrencyPairAndSide')->willReturn($collection);
        $result = (new GetOrderBookUseCase($repo))->execute(1, 'sell');
        self::assertSame([], $result->items[0]->paymentMethodIds);
    }

    public function testInvalidJsonPaymentMethodIdsDecodedAsEmptyArray(): void
    {
        $entry = $this->createStub(OrderBookEntry::class);
        $entry->method('getId')->willReturn(3);
        $entry->method('getUfBybitOrderId')->willReturn('order-789');
        $entry->method('getUfSide')->willReturn('buy');
        $entry->method('getUfPrice')->willReturn(94.0);
        $entry->method('getUfQuantity')->willReturn(200.0);
        $entry->method('getUfMinAmount')->willReturn(100.0);
        $entry->method('getUfMaxAmount')->willReturn(5000.0);
        $entry->method('getUfCounterpartyName')->willReturn('Trader3');
        $entry->method('getUfCounterpartyRating')->willReturn(0.0);
        $entry->method('getUfCounterpartyTrades')->willReturn(10);
        $entry->method('getUfCounterpartyCompletionRate')->willReturn(0.5);
        $entry->method('getUfPaymentMethodIds')->willReturn('"not_array"');
        $entry->method('getUfPaymentTimeLimit')->willReturn(15);
        $collection = $this->createStub(OrderBookEntryCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$entry]));
        $repo = $this->createStub(OrderBookRepository::class);
        $repo->method('findByCurrencyPairAndSide')->willReturn($collection);
        $result = (new GetOrderBookUseCase($repo))->execute(1, 'buy');
        self::assertSame([], $result->items[0]->paymentMethodIds);
    }
}
