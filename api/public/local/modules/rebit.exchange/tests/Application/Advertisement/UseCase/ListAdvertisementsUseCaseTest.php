<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Advertisement\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Advertisement\Dto\Result\AdvertisementListResultDto;
use Rebit\Exchange\Application\Advertisement\UseCase\ListAdvertisementsUseCase;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;
use Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Bitrix\Main\Type\DateTime;

/**
 * @internal
 */
final class ListAdvertisementsUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testReturnsEmptyListWhenNoAdvertisements(): void
    {
        $collection = $this->createStub(AdvertisementCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $repo = $this->createMock(AdvertisementRepository::class);
        $repo
            ->expects($this->once())
            ->method('findByUserId')
            ->with(self::USER_ID, null)
            ->willReturn($collection)
        ;

        $result = (new ListAdvertisementsUseCase($repo))->execute(self::USER_ID);

        self::assertInstanceOf(AdvertisementListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithItems(): void
    {
        $ad = $this->createAdStub(1, 'active');

        $collection = $this->createStub(AdvertisementCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$ad]));

        $repo = $this->createStub(AdvertisementRepository::class);
        $repo->method('findByUserId')->willReturn($collection);

        $result = (new ListAdvertisementsUseCase($repo))->execute(self::USER_ID);

        self::assertCount(1, $result->items);
        self::assertSame(1, $result->items[0]->id);
        self::assertSame('active', $result->items[0]->status);
    }

    public function testFiltersByStatus(): void
    {
        $collection = $this->createStub(AdvertisementCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $repo = $this->createMock(AdvertisementRepository::class);
        $repo
            ->expects($this->once())
            ->method('findByUserId')
            ->with(self::USER_ID, 'active')
            ->willReturn($collection)
        ;

        (new ListAdvertisementsUseCase($repo))->execute(self::USER_ID, 'active');
    }

    private function createAdStub(int $id, string $status): Advertisement
    {
        $now = new DateTime();
        $ad = $this->createStub(Advertisement::class);
        $ad->method('getId')->willReturn($id);
        $ad->method('getUfBybitAdId')->willReturn('bybit-' . $id);
        $ad->method('getUfCurrencyPairId')->willReturn(1);
        $ad->method('getUfSide')->willReturn('buy');
        $ad->method('getUfPriceType')->willReturn('fixed');
        $ad->method('getUfPrice')->willReturn(95.0);
        $ad->method('getUfPremium')->willReturn(0.0);
        $ad->method('getUfQuantity')->willReturn(100.0);
        $ad->method('getUfQuantityRemaining')->willReturn(100.0);
        $ad->method('getUfMinAmount')->willReturn(1000.0);
        $ad->method('getUfMaxAmount')->willReturn(50000.0);
        $ad->method('getUfPaymentMethodIds')->willReturn('[]');
        $ad->method('getUfPaymentPeriod')->willReturn(15);
        $ad->method('getUfFeeRate')->willReturn(0.0);
        $ad->method('getUfConditions')->willReturn('');
        $ad->method('getUfChatScriptId')->willReturn(0);
        $ad->method('getUfStatus')->willReturn($status);
        $ad->method('getUfCreatedAt')->willReturn($now);
        $ad->method('getUfUpdatedAt')->willReturn($now);

        return $ad;
    }
}
