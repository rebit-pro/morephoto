<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Trade\Dto\Result\TradeListResultDto;
use Rebit\Exchange\Application\Trade\UseCase\ListTradesUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Entity\TradeCollection;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;

/**
 * @internal
 */
final class ListTradesUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testReturnsEmptyList(): void
    {
        $collection = $this->createStub(TradeCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $repo = $this->createMock(TradeRepository::class);
        $repo
            ->expects($this->once())
            ->method('findByUserId')
            ->with(self::USER_ID, null)
            ->willReturn($collection)
        ;

        $result = (new ListTradesUseCase($repo))->execute(self::USER_ID);

        self::assertInstanceOf(TradeListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithTrades(): void
    {
        $trade = $this->createTradeStub(1);

        $collection = $this->createStub(TradeCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$trade]));

        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findByUserId')->willReturn($collection);

        $result = (new ListTradesUseCase($repo))->execute(self::USER_ID);

        self::assertCount(1, $result->items);
        self::assertSame(1, $result->items[0]->id);
        self::assertSame('pending_payment', $result->items[0]->status);
    }

    public function testFiltersByStatus(): void
    {
        $collection = $this->createStub(TradeCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $repo = $this->createMock(TradeRepository::class);
        $repo
            ->expects($this->once())
            ->method('findByUserId')
            ->with(self::USER_ID, 'completed')
            ->willReturn($collection)
        ;

        (new ListTradesUseCase($repo))->execute(self::USER_ID, 'completed');
    }

    private function createTradeStub(int $id): Trade
    {
        $now = new DateTime();
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn($id);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-' . $id);
        $trade->method('getUfBybitStatus')->willReturn(10);
        $trade->method('getUfSide')->willReturn('buy');
        $trade->method('getUfPrice')->willReturn(95.0);
        $trade->method('getUfQuantity')->willReturn(100.0);
        $trade->method('getUfFiatAmount')->willReturn(9500.0);
        $trade->method('getUfFee')->willReturn(0.0);
        $trade->method('getUfStatus')->willReturn('pending_payment');
        $trade->method('getUfCounterpartyName')->willReturn('User2');
        $trade->method('getUfCurrencyPairId')->willReturn(1);
        $trade->method('getUfAdvertisementId')->willReturn(0);
        $trade->method('getUfPaymentDeadline')->willReturn($now);
        $trade->method('getUfPaidAt')->willReturn(null);
        $trade->method('getUfCompletedAt')->willReturn(null);
        $trade->method('getUfCancelledAt')->willReturn(null);
        $trade->method('getUfCancelReason')->willReturn('');
        $trade->method('getUfCreatedAt')->willReturn($now);
        $trade->method('getUfUpdatedAt')->willReturn($now);

        return $trade;
    }
}
