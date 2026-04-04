<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmPaymentUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Shared\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class ConfirmPaymentUseCaseTest extends TestCase
{
    private const int BUYER_ID = 10;
    private const int SELLER_ID = 20;

    public function testSuccessfulPaymentConfirmation(): void
    {
        $trade = $this->createTradeMock();

        $trade
            ->expects($this->once())
            ->method('setUfStatus')
            ->with('payment_sent')
        ;
        $trade
            ->expects($this->once())
            ->method('setUfPaidAt')
            ->with($this->isInstanceOf(DateTime::class))
        ;

        $repo = $this->createMock(TradeRepository::class);
        $repo->method('findById')->with(1)->willReturn($trade);
        $repo->expects($this->once())->method('save')->with($trade);

        $gateway = $this->createMock(BybitTradeGatewayInterface::class);
        $gateway
            ->expects($this->once())
            ->method('confirmPayment')
            ->with(self::BUYER_ID, 'bybit-order-1', 'bank_transfer', 'pay-123')
        ;

        $result = (new ConfirmPaymentUseCase($repo, $gateway))
            ->execute(1, self::BUYER_ID, 'bank_transfer', 'pay-123')
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

        (new ConfirmPaymentUseCase($repo, $gateway))
            ->execute(1, self::BUYER_ID, 'bank_transfer', 'pay-123')
        ;
    }

    public function testOnlyBuyerCanConfirmPaymentThrows403(): void
    {
        $trade = $this->createTradeStub();

        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn($trade);

        $gateway = $this->createStub(BybitTradeGatewayInterface::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new ConfirmPaymentUseCase($repo, $gateway))
            ->execute(1, self::SELLER_ID, 'bank_transfer', 'pay-123')
        ;
    }

    private function createTradeStub(): Trade
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');

        return $trade;
    }

    private function createTradeMock(): Trade
    {
        $now = new DateTime();
        $trade = $this->createMock(Trade::class);
        $trade->method('getId')->willReturn(1);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');
        $trade->method('getUfBybitStatus')->willReturn(10);
        $trade->method('getUfSide')->willReturn('buy');
        $trade->method('getUfPrice')->willReturn(95.0);
        $trade->method('getUfQuantity')->willReturn(100.0);
        $trade->method('getUfFiatAmount')->willReturn(9500.0);
        $trade->method('getUfFee')->willReturn(0.0);
        $trade->method('getUfStatus')->willReturn('payment_sent');
        $trade->method('getUfCounterpartyName')->willReturn('User2');
        $trade->method('getUfCurrencyPairId')->willReturn(1);
        $trade->method('getUfAdvertisementId')->willReturn(0);
        $trade->method('getUfPaymentDeadline')->willReturn($now);
        $trade->method('getUfPaidAt')->willReturn($now);
        $trade->method('getUfCompletedAt')->willReturn(null);
        $trade->method('getUfCancelledAt')->willReturn(null);
        $trade->method('getUfCancelReason')->willReturn('');
        $trade->method('getUfCreatedAt')->willReturn($now);
        $trade->method('getUfUpdatedAt')->willReturn($now);

        return $trade;
    }
}
