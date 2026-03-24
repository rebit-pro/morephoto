<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\OrderBook\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookBothSidesResultDto;
use Rebit\Exchange\Application\OrderBook\UseCase\GetOrderBookUseCase;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class GetOrderBookUseCaseTest extends TestCase
{
    private function makeCollection(array $entries): OrderBookEntryCollection
    {
        $collection = $this->createStub(OrderBookEntryCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator($entries));

        return $collection;
    }

    private function makeUseCase(
        OrderBookRepository $orderBookRepo,
        CurrencyPairRepository $pairRepo,
        ?PaymentMethodRepository $paymentMethodRepo = null,
    ): GetOrderBookUseCase {
        if (null === $paymentMethodRepo) {
            // По умолчанию: маппинг Bybit ID не находит совпадений → Bybit IDs возвращаются как есть
            $paymentMethodRepo = $this->createStub(PaymentMethodRepository::class);
            $paymentMethodRepo->method('mapBybitIdsToCode')->willReturn([]);
        }

        return new GetOrderBookUseCase($orderBookRepo, $pairRepo, $paymentMethodRepo);
    }

    public function testThrows404WhenPairNotFound(): void
    {
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findByTokenAndFiat')->willReturn(null);
        $orderBookRepo = $this->createMock(OrderBookRepository::class);
        $orderBookRepo->expects($this->never())->method('findByCurrencyPairAndSide');
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->makeUseCase($orderBookRepo, $pairRepo)->execute('USDT', 'RUB');
    }

    public function testReturnsBothSidesWhenPairFound(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(1);
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findByTokenAndFiat')->willReturn($pair);
        $orderBookRepo = $this->createStub(OrderBookRepository::class);
        $orderBookRepo
            ->method('findByCurrencyPairAndSide')
            ->willReturn($this->makeCollection([]))
        ;
        $result = $this->makeUseCase($orderBookRepo, $pairRepo)->execute('USDT', 'RUB');
        self::assertInstanceOf(OrderBookBothSidesResultDto::class, $result);
        self::assertSame([], $result->buy);
        self::assertSame([], $result->sell);
    }

    public function testMapsEntryFieldsCorrectly(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(1);
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findByTokenAndFiat')->willReturn($pair);
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
        $orderBookRepo = $this->createStub(OrderBookRepository::class);
        $orderBookRepo
            ->method('findByCurrencyPairAndSide')
            ->willReturnCallback(fn(int $id, string $side) => match ($side) {
                'buy' => $this->makeCollection([$entry]),
                default => $this->makeCollection([]),
            })
        ;
        $result = $this->makeUseCase($orderBookRepo, $pairRepo)->execute('USDT', 'RUB');
        self::assertCount(1, $result->buy);
        self::assertCount(0, $result->sell);
        $item = $result->buy[0];
        self::assertSame(1, $item->id);
        self::assertSame('order-123', $item->bybitOrderId);
        self::assertSame('buy', $item->side);
        self::assertSame(95.5, $item->price);
        self::assertSame(100.0, $item->amount);
        self::assertSame(1000.0, $item->minLimit);
        self::assertSame(50000.0, $item->maxLimit);
        self::assertSame('Trader1', $item->username);
        self::assertSame(4.8, $item->counterpartyRating);
        self::assertSame(150, $item->completedTrades);
        self::assertSame(0.98, $item->completionRate);
        self::assertSame(['pm_1', 'pm_2'], $item->paymentMethods);
        self::assertSame(15, $item->paymentTimeLimit);
    }

    public function testInvalidJsonPaymentMethodsDecodedAsEmptyArray(): void
    {
        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getId')->willReturn(2);
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findByTokenAndFiat')->willReturn($pair);
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
        $orderBookRepo = $this->createStub(OrderBookRepository::class);
        $orderBookRepo
            ->method('findByCurrencyPairAndSide')
            ->willReturn($this->makeCollection([$entry]))
        ;
        $result = $this->makeUseCase($orderBookRepo, $pairRepo)->execute('USDT', 'RUB');
        self::assertSame([], $result->buy[0]->paymentMethods);
    }
}
