<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Currency\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyPairListResultDto;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrencyPairsUseCase;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;

/**
 * @internal
 */
final class GetCurrencyPairsUseCaseTest extends TestCase
{
    public function testReturnsEmptyListWhenNoPairs(): void
    {
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $repo = $this->createMock(CurrencyPairRepository::class);
        $repo
            ->expects($this->once())
            ->method('findActive')
            ->willReturn($collection)
        ;
        $result = (new GetCurrencyPairsUseCase($repo))->execute();
        self::assertInstanceOf(CurrencyPairListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithPairs(): void
    {
        $pair1 = $this->createStub(CurrencyPair::class);
        $pair1->method('getId')->willReturn(1);
        $pair1->method('getUfCode')->willReturn('USDT_RUB');
        $pair1->method('getUfTokenCurrencyId')->willReturn(10);
        $pair1->method('getUfFiatCurrencyId')->willReturn(20);
        $pair1->method('getUfIsDefault')->willReturn(true);
        $pair1->method('getUfSort')->willReturn(100);
        $pair2 = $this->createStub(CurrencyPair::class);
        $pair2->method('getId')->willReturn(2);
        $pair2->method('getUfCode')->willReturn('BTC_RUB');
        $pair2->method('getUfTokenCurrencyId')->willReturn(30);
        $pair2->method('getUfFiatCurrencyId')->willReturn(20);
        $pair2->method('getUfIsDefault')->willReturn(false);
        $pair2->method('getUfSort')->willReturn(200);
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$pair1, $pair2]));
        $repo = $this->createStub(CurrencyPairRepository::class);
        $repo->method('findActive')->willReturn($collection);
        $result = (new GetCurrencyPairsUseCase($repo))->execute();
        self::assertCount(2, $result->items);
        self::assertSame(1, $result->items[0]->id);
        self::assertSame('USDT_RUB', $result->items[0]->code);
        self::assertSame(10, $result->items[0]->tokenCurrencyId);
        self::assertSame(20, $result->items[0]->fiatCurrencyId);
        self::assertTrue($result->items[0]->isDefault);
        self::assertSame(100, $result->items[0]->sort);
        self::assertSame(2, $result->items[1]->id);
        self::assertSame('BTC_RUB', $result->items[1]->code);
        self::assertFalse($result->items[1]->isDefault);
    }
}
