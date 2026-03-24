<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Currency\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyPairListResultDto;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrencyPairsUseCase;
use Rebit\Exchange\Domain\Currency\Entity\Currency;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;

/**
 * @internal
 */
final class GetCurrencyPairsUseCaseTest extends TestCase
{
    private function makeUseCase(
        CurrencyPairRepository $pairRepo,
        CurrencyRepository $currencyRepo,
    ): GetCurrencyPairsUseCase {
        return new GetCurrencyPairsUseCase($pairRepo, $currencyRepo);
    }

    private function emptyCurrencyRepo(): CurrencyRepository
    {
        $collection = $this->createStub(CurrencyCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $repo = $this->createStub(CurrencyRepository::class);
        $repo->method('findActive')->willReturn($collection);

        return $repo;
    }

    public function testReturnsEmptyListWhenNoPairs(): void
    {
        $collection = $this->createStub(CurrencyPairCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $repo = $this->createMock(CurrencyPairRepository::class);
        $repo->expects($this->once())->method('findActive')->willReturn($collection);

        $result = $this->makeUseCase($repo, $this->emptyCurrencyRepo())->execute();

        self::assertInstanceOf(CurrencyPairListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithPairs(): void
    {
        $usdtCurrency = $this->createStub(Currency::class);
        $usdtCurrency->method('getId')->willReturn(10);
        $usdtCurrency->method('getUfCode')->willReturn('USDT');

        $rubCurrency = $this->createStub(Currency::class);
        $rubCurrency->method('getId')->willReturn(20);
        $rubCurrency->method('getUfCode')->willReturn('RUB');

        $btcCurrency = $this->createStub(Currency::class);
        $btcCurrency->method('getId')->willReturn(30);
        $btcCurrency->method('getUfCode')->willReturn('BTC');

        $currencyCollection = $this->createStub(CurrencyCollection::class);
        $currencyCollection->method('getIterator')->willReturn(new \ArrayIterator([$usdtCurrency, $rubCurrency, $btcCurrency]));
        $currencyRepo = $this->createStub(CurrencyRepository::class);
        $currencyRepo->method('findActive')->willReturn($currencyCollection);

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

        $pairCollection = $this->createStub(CurrencyPairCollection::class);
        $pairCollection->method('getIterator')->willReturn(new \ArrayIterator([$pair1, $pair2]));
        $pairRepo = $this->createStub(CurrencyPairRepository::class);
        $pairRepo->method('findActive')->willReturn($pairCollection);

        $result = $this->makeUseCase($pairRepo, $currencyRepo)->execute();

        self::assertCount(2, $result->items);

        self::assertSame(1, $result->items[0]->id);
        self::assertSame('USDT_RUB', $result->items[0]->code);
        self::assertSame(10, $result->items[0]->tokenCurrencyId);
        self::assertSame(20, $result->items[0]->fiatCurrencyId);
        self::assertSame('USDT', $result->items[0]->tokenCode);
        self::assertSame('RUB', $result->items[0]->fiatCode);
        self::assertTrue($result->items[0]->isDefault);
        self::assertSame(100, $result->items[0]->sort);

        self::assertSame(2, $result->items[1]->id);
        self::assertSame('BTC_RUB', $result->items[1]->code);
        self::assertSame('BTC', $result->items[1]->tokenCode);
        self::assertSame('RUB', $result->items[1]->fiatCode);
        self::assertFalse($result->items[1]->isDefault);
    }
}
