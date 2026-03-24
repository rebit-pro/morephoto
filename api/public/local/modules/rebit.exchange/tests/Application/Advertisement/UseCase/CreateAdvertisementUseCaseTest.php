<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Advertisement\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Advertisement\Dto\Request\CreateAdvertisementRequestDto;
use Rebit\Exchange\Application\Advertisement\Dto\Result\AdvertisementResultDto;
use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Exchange\Application\Advertisement\UseCase\CreateAdvertisementUseCase;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Share\Application\Contract\Wallet\BalanceQueryInterface;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Bitrix\Main\Type\DateTime;

/**
 * @internal
 */
final class CreateAdvertisementUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    private function createUseCase(
        AdvertisementRepository $advertisementRepository,
        CurrencyPairRepository $currencyPairRepository,
        BybitAdvertisementGatewayInterface $bybitGateway,
        BalanceQueryInterface $balanceQuery,
    ): CreateAdvertisementUseCase {
        return new CreateAdvertisementUseCase(
            advertisementRepository: $advertisementRepository,
            currencyPairRepository: $currencyPairRepository,
            bybitGateway: $bybitGateway,
            balanceQuery: $balanceQuery,
        );
    }

    private function createDefaultDto(string $side = 'buy'): CreateAdvertisementRequestDto
    {
        return new CreateAdvertisementRequestDto(
            currencyPairId: 1,
            side: $side,
            priceType: 'fixed',
            price: '95.50',
            premium: null,
            quantity: '100',
            minAmount: '1000',
            maxAmount: '50000',
            paymentMethodIds: ['pm_1', 'pm_2'],
            paymentPeriod: 15,
            conditions: 'Быстрая оплата',
        );
    }

    public function testSuccessfulBuyAdvertisementCreation(): void
    {
        $dto = $this->createDefaultDto('buy');

        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getUfCode')->willReturn('USDT_RUB');
        $pair->method('getUfTokenCurrencyId')->willReturn(10);

        $currencyPairRepo = $this->createMock(CurrencyPairRepository::class);
        $currencyPairRepo
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($pair)
        ;

        $bybitGateway = $this->createMock(BybitAdvertisementGatewayInterface::class);
        $bybitGateway
            ->expects($this->once())
            ->method('create')
            ->with(self::USER_ID, $this->isType('array'))
            ->willReturn('bybit-ad-123')
        ;

        $balanceQuery = $this->createStub(BalanceQueryInterface::class);
        // Для buy баланс не проверяется — не должен вызываться
        $balanceQuery->method('hasAvailableBalance')->willReturn(false);

        $ad = $this->createAdStub();

        $adRepo = $this->createMock(AdvertisementRepository::class);
        $adRepo
            ->expects($this->once())
            ->method('create')
            ->willReturn($ad)
        ;

        $result = $this->createUseCase($adRepo, $currencyPairRepo, $bybitGateway, $balanceQuery)
            ->execute($dto, self::USER_ID)
        ;

        self::assertInstanceOf(AdvertisementResultDto::class, $result);
        self::assertSame(1, $result->id);
        self::assertSame('bybit-ad-123', $result->bybitAdId);
        self::assertSame('buy', $result->side);
    }

    public function testSuccessfulSellAdvertisementWithSufficientBalance(): void
    {
        $dto = $this->createDefaultDto('sell');

        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getUfCode')->willReturn('USDT_RUB');
        $pair->method('getUfTokenCurrencyId')->willReturn(10);

        $currencyPairRepo = $this->createStub(CurrencyPairRepository::class);
        $currencyPairRepo->method('findById')->willReturn($pair);

        $balanceQuery = $this->createMock(BalanceQueryInterface::class);
        $balanceQuery
            ->expects($this->once())
            ->method('hasAvailableBalance')
            ->with(self::USER_ID, 10, 100.0)
            ->willReturn(true)
        ;

        $bybitGateway = $this->createStub(BybitAdvertisementGatewayInterface::class);
        $bybitGateway->method('create')->willReturn('bybit-ad-456');

        $ad = $this->createAdStub();
        $adRepo = $this->createStub(AdvertisementRepository::class);
        $adRepo->method('create')->willReturn($ad);

        $result = $this->createUseCase($adRepo, $currencyPairRepo, $bybitGateway, $balanceQuery)
            ->execute($dto, self::USER_ID)
        ;

        self::assertInstanceOf(AdvertisementResultDto::class, $result);
    }

    public function testSellAdvertisementWithInsufficientBalanceThrowsValidation(): void
    {
        $dto = $this->createDefaultDto('sell');

        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getUfCode')->willReturn('USDT_RUB');
        $pair->method('getUfTokenCurrencyId')->willReturn(10);

        $currencyPairRepo = $this->createStub(CurrencyPairRepository::class);
        $currencyPairRepo->method('findById')->willReturn($pair);

        $balanceQuery = $this->createMock(BalanceQueryInterface::class);
        $balanceQuery
            ->expects($this->once())
            ->method('hasAvailableBalance')
            ->with(self::USER_ID, 10, 100.0)
            ->willReturn(false)
        ;

        $bybitGateway = $this->createStub(BybitAdvertisementGatewayInterface::class);
        $adRepo = $this->createStub(AdvertisementRepository::class);

        $this->expectException(ValidationHttpException::class);
        $this->expectExceptionMessage('Недостаточно средств для создания объявления на продажу');

        $this->createUseCase($adRepo, $currencyPairRepo, $bybitGateway, $balanceQuery)
            ->execute($dto, self::USER_ID)
        ;
    }

    public function testCurrencyPairNotFoundThrows404(): void
    {
        $dto = $this->createDefaultDto();

        $currencyPairRepo = $this->createStub(CurrencyPairRepository::class);
        $currencyPairRepo->method('findById')->willReturn(null);

        $bybitGateway = $this->createStub(BybitAdvertisementGatewayInterface::class);
        $balanceQuery = $this->createStub(BalanceQueryInterface::class);
        $adRepo = $this->createStub(AdvertisementRepository::class);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Валютная пара не найдена');

        $this->createUseCase($adRepo, $currencyPairRepo, $bybitGateway, $balanceQuery)
            ->execute($dto, self::USER_ID)
        ;
    }

    public function testInvalidCurrencyPairCodeFormatThrowsValidation(): void
    {
        $dto = $this->createDefaultDto();

        $pair = $this->createStub(CurrencyPair::class);
        $pair->method('getUfCode')->willReturn('INVALID');

        $currencyPairRepo = $this->createStub(CurrencyPairRepository::class);
        $currencyPairRepo->method('findById')->willReturn($pair);

        $bybitGateway = $this->createStub(BybitAdvertisementGatewayInterface::class);
        $balanceQuery = $this->createStub(BalanceQueryInterface::class);
        $adRepo = $this->createStub(AdvertisementRepository::class);

        $this->expectException(ValidationHttpException::class);
        $this->expectExceptionMessage('Некорректный формат кода валютной пары');

        $this->createUseCase($adRepo, $currencyPairRepo, $bybitGateway, $balanceQuery)
            ->execute($dto, self::USER_ID)
        ;
    }

    private function createAdStub(): Advertisement
    {
        $now = new DateTime();
        $ad = $this->createStub(Advertisement::class);
        $ad->method('getId')->willReturn(1);
        $ad->method('getUfBybitAdId')->willReturn('bybit-ad-123');
        $ad->method('getUfCurrencyPairId')->willReturn(1);
        $ad->method('getUfSide')->willReturn('buy');
        $ad->method('getUfPriceType')->willReturn('fixed');
        $ad->method('getUfPrice')->willReturn(95.5);
        $ad->method('getUfPremium')->willReturn(0.0);
        $ad->method('getUfQuantity')->willReturn(100.0);
        $ad->method('getUfQuantityRemaining')->willReturn(100.0);
        $ad->method('getUfMinAmount')->willReturn(1000.0);
        $ad->method('getUfMaxAmount')->willReturn(50000.0);
        $ad->method('getUfPaymentMethodIds')->willReturn('["pm_1","pm_2"]');
        $ad->method('getUfPaymentPeriod')->willReturn(15);
        $ad->method('getUfFeeRate')->willReturn(0.0);
        $ad->method('getUfConditions')->willReturn('Быстрая оплата');
        $ad->method('getUfChatScriptId')->willReturn(0);
        $ad->method('getUfStatus')->willReturn('active');
        $ad->method('getUfCreatedAt')->willReturn($now);
        $ad->method('getUfUpdatedAt')->willReturn($now);

        return $ad;
    }
}
