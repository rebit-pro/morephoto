<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Application\Trade\UseCase\GetTradeUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class GetTradeUseCaseTest extends TestCase
{
    private const int BUYER_ID = 10;
    private const int SELLER_ID = 20;

    public function testSuccessfulGetTradeByBuyer(): void
    {
        $trade = $this->createTradeStub('pending_payment');

        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn($trade);

        $gateway = $this->createMock(BybitTradeGatewayInterface::class);
        $gateway
            ->expects($this->once())
            ->method('fetchOrderInfo')
            ->with(self::BUYER_ID, 'bybit-order-1')
            ->willReturn(['status' => 10])
        ;

        $result = (new GetTradeUseCase($repo, $gateway))->execute(1, self::BUYER_ID);

        self::assertInstanceOf(TradeResultDto::class, $result);
        self::assertSame(1, $result->id);
    }

    public function testBybitStatusUpdateSavesToRepository(): void
    {
        $trade = $this->createTradeMock('pending_payment', 10);

        $trade
            ->expects($this->once())
            ->method('setUfBybitStatus')
            ->with(20)
        ;
        $trade
            ->expects($this->once())
            ->method('setUfStatus')
            ->with('payment_sent')
        ;

        $repo = $this->createMock(TradeRepository::class);
        $repo->method('findById')->willReturn($trade);
        $repo->expects($this->once())->method('save')->with($trade);

        $gateway = $this->createStub(BybitTradeGatewayInterface::class);
        $gateway->method('fetchOrderInfo')->willReturn(['status' => 20]);

        (new GetTradeUseCase($repo, $gateway))->execute(1, self::BUYER_ID);
    }

    public function testCompletedTradeDoesNotCallBybit(): void
    {
        $trade = $this->createTradeStub('completed');

        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn($trade);

        $gateway = $this->createMock(BybitTradeGatewayInterface::class);
        $gateway
            ->expects($this->never())
            ->method('fetchOrderInfo')
        ;

        (new GetTradeUseCase($repo, $gateway))->execute(1, self::BUYER_ID);
    }

    public function testBybitErrorDoesNotBlockTradeDisplay(): void
    {
        $trade = $this->createTradeStub('pending_payment');

        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn($trade);

        $gateway = $this->createStub(BybitTradeGatewayInterface::class);
        $gateway->method('fetchOrderInfo')->willThrowException(new HttpException('Bybit error'));

        $result = (new GetTradeUseCase($repo, $gateway))->execute(1, self::BUYER_ID);

        self::assertInstanceOf(TradeResultDto::class, $result);
    }

    public function testTradeNotFoundThrows404(): void
    {
        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn(null);

        $gateway = $this->createStub(BybitTradeGatewayInterface::class);

        $this->expectException(EntityNotFoundException::class);

        (new GetTradeUseCase($repo, $gateway))->execute(1, self::BUYER_ID);
    }

    public function testAccessDeniedThrows403(): void
    {
        $trade = $this->createTradeStub('pending_payment');

        $repo = $this->createStub(TradeRepository::class);
        $repo->method('findById')->willReturn($trade);

        $gateway = $this->createStub(BybitTradeGatewayInterface::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new GetTradeUseCase($repo, $gateway))->execute(1, 999);
    }

    private function createTradeStub(string $status): Trade
    {
        $now = new DateTime();
        $trade = $this->createStub(Trade::class);
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
        $trade->method('getUfStatus')->willReturn($status);
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

    private function createTradeMock(string $status, int $bybitStatus): Trade
    {
        $now = new DateTime();
        $trade = $this->createMock(Trade::class);
        $trade->method('getId')->willReturn(1);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');
        $trade->method('getUfBybitStatus')->willReturn($bybitStatus);
        $trade->method('getUfSide')->willReturn('buy');
        $trade->method('getUfPrice')->willReturn(95.0);
        $trade->method('getUfQuantity')->willReturn(100.0);
        $trade->method('getUfFiatAmount')->willReturn(9500.0);
        $trade->method('getUfFee')->willReturn(0.0);
        $trade->method('getUfStatus')->willReturn($status);
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
