<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmReceiptUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class ConfirmReceiptUseCaseTest extends TestCase
{
    private const int BUYER_ID = 10;
    private const int SELLER_ID = 20;

    public function testSuccessfulReceiptConfirmation(): void
    {
        $trade = $this->createTradeMock();

        $trade
            ->expects($this->once())
            ->method('setUfStatus')
            ->with('completed')
        ;
        $trade
            ->expects($this->once())
            ->method('setUfCompletedAt')
            ->with($this->isInstanceOf(DateTime::class))
        ;

        $repo = $this->createMock(TradeRepository::class);
        $repo->method('findById')->with(1)->willReturn($trade);
        $repo->expects($this->once())->method('save')->with($trade);

        $gateway = $this->createMock(BybitTradeGatewayInterface::class);
        $gateway
            ->expects($this->once())
            ->method('releaseAssets')
            ->with(self::SELLER_ID, 'bybit-order-1')
        ;

        $result = (new ConfirmReceiptUseCase($repo, $gateway))
            ->execute(1, self::SELLER_ID)
        ;

        self::assertInstanceOf(TradeResultDto::class, $result);
        self::assertSame(1, $result->id);
    }

    public function testTradeNotFoundThrows404(): void
    {
        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn(null);

        $gateway = $this->createStub(BybitTradeGatewayInterface::class);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Сделка не найдена');

        (new ConfirmReceiptUseCase($repo, $gateway))->execute(1, self::SELLER_ID);
    }

    public function testOnlySellerCanConfirmReceiptThrows403(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);

        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn($trade);

        $gateway = $this->createStub(BybitTradeGatewayInterface::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new ConfirmReceiptUseCase($repo, $gateway))->execute(1, self::BUYER_ID);
    }

    private function createTradeMock(): Trade
    {
        $now = new DateTime();
        $trade = $this->createMock(Trade::class);
        $trade->method('getId')->willReturn(1);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');
        $trade->method('getUfBybitStatus')->willReturn(20);
        $trade->method('getUfSide')->willReturn('sell');
        $trade->method('getUfPrice')->willReturn(95.0);
        $trade->method('getUfQuantity')->willReturn(100.0);
        $trade->method('getUfFiatAmount')->willReturn(9500.0);
        $trade->method('getUfFee')->willReturn(0.0);
        $trade->method('getUfStatus')->willReturn('completed');
        $trade->method('getUfCounterpartyName')->willReturn('User1');
        $trade->method('getUfCurrencyPairId')->willReturn(1);
        $trade->method('getUfAdvertisementId')->willReturn(0);
        $trade->method('getUfPaymentDeadline')->willReturn($now);
        $trade->method('getUfPaidAt')->willReturn($now);
        $trade->method('getUfCompletedAt')->willReturn($now);
        $trade->method('getUfCancelledAt')->willReturn(null);
        $trade->method('getUfCancelReason')->willReturn('');
        $trade->method('getUfCreatedAt')->willReturn($now);
        $trade->method('getUfUpdatedAt')->willReturn($now);

        return $trade;
    }
}
