<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Currency\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyListResultDto;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrenciesUseCase;
use Rebit\Exchange\Domain\Currency\Entity\Currency;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;

/**
 * @internal
 */
final class GetCurrenciesUseCaseTest extends TestCase
{
    public function testReturnsEmptyListWhenNoCurrencies(): void
    {
        $collection = $this->createStub(CurrencyCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $repo = $this->createMock(CurrencyRepository::class);
        $repo
            ->expects($this->once())
            ->method('findActive')
            ->willReturn($collection)
        ;
        $result = (new GetCurrenciesUseCase($repo))->execute();
        self::assertInstanceOf(CurrencyListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithCurrencies(): void
    {
        $currency1 = $this->createStub(Currency::class);
        $currency1->method('getId')->willReturn(1);
        $currency1->method('getUfCode')->willReturn('USDT');
        $currency1->method('getUfName')->willReturn('Tether');
        $currency1->method('getUfType')->willReturn('crypto');
        $currency1->method('getUfDecimals')->willReturn(2);
        $currency1->method('getUfSort')->willReturn(100);
        $currency2 = $this->createStub(Currency::class);
        $currency2->method('getId')->willReturn(2);
        $currency2->method('getUfCode')->willReturn('RUB');
        $currency2->method('getUfName')->willReturn('Рубль');
        $currency2->method('getUfType')->willReturn('fiat');
        $currency2->method('getUfDecimals')->willReturn(2);
        $currency2->method('getUfSort')->willReturn(200);
        $collection = $this->createStub(CurrencyCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$currency1, $currency2]));
        $repo = $this->createStub(CurrencyRepository::class);
        $repo->method('findActive')->willReturn($collection);
        $result = (new GetCurrenciesUseCase($repo))->execute();
        self::assertCount(2, $result->items);
        self::assertSame(1, $result->items[0]->id);
        self::assertSame('USDT', $result->items[0]->code);
        self::assertSame('Tether', $result->items[0]->name);
        self::assertSame('crypto', $result->items[0]->type);
        self::assertSame(2, $result->items[0]->decimals);
        self::assertSame(100, $result->items[0]->sort);
        self::assertSame(2, $result->items[1]->id);
        self::assertSame('RUB', $result->items[1]->code);
        self::assertSame('fiat', $result->items[1]->type);
    }
}
