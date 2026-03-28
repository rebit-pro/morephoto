<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\Message\Handler;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderInfoDto;
use Rebit\Exchange\Application\Trade\Message\Handler\TradeDiscoveredMessageHandler;
use Rebit\Exchange\Application\Trade\Message\TradeDiscoveredMessage;
use Rebit\Exchange\Application\Trade\UseCase\EnrichTradeFromBybitUseCase;
use Rebit\Exchange\Application\Trade\UseCase\SyncCounterpartyUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\StartTradeChatScriptUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;

/**
 * @internal
 */
final class TradeDiscoveredMessageHandlerTest extends TestCase
{
    public function testLogsWarningWhenTradeNotFound(): void
    {
        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn(null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $handler = new TradeDiscoveredMessageHandler(
            $tradeRepository,
            $this->createStub(EnrichTradeFromBybitUseCase::class),
            $this->createStub(SyncCounterpartyUseCase::class),
            $this->createStub(NotificationPublisherInterface::class),
            $this->createStub(StartTradeChatScriptUseCase::class),
            $logger,
        );

        $handler(new TradeDiscoveredMessage(tradeId: 10, bybitOrderId: 'order-1', fiatAmount: '100.50'));
    }

    public function testSuccessfulFlowCallsDependencies(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(10);
        $trade->method('getUfBybitOrderId')->willReturn('order-1');
        $trade->method('getUfSide')->willReturn('buy');
        $trade->method('getUfBuyerUserId')->willReturn(42);
        $trade->method('getUfCounterpartyName')->willReturn('Bob');

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($trade);

        $enrichCalled = 0;
        $enrichUseCase = $this->createStub(EnrichTradeFromBybitUseCase::class);
        $orderInfo = new BybitTradeOrderInfoDto(
            id: 'order-1',
            side: 0,
            itemId: '',
            userId: '',
            nickName: '',
            makerUserId: '',
            targetUserId: '100',
            targetNickName: '',
            tokenId: '',
            currencyId: '',
            price: '',
            quantity: '',
            amount: '',
            paymentType: 0,
            transferDate: '',
            status: 0,
            createDate: '',
            paymentTermList: [],
            remark: '',
            transferLastSeconds: '',
        );

        $enrichUseCase->method('execute')->willReturnCallback(function(Trade $actualTrade) use ($trade, &$enrichCalled, $orderInfo): BybitTradeOrderInfoDto {
            ++$enrichCalled;
            if ($actualTrade !== $trade) {
                throw new AssertionFailedError('Unexpected trade in enrich use case');
            }

            return $orderInfo;
        });

        $syncCalled = 0;
        $syncUseCase = $this->createStub(SyncCounterpartyUseCase::class);
        $syncUseCase->method('execute')->willReturnCallback(function(Trade $actualTrade, BybitTradeOrderInfoDto $actualOrderInfo) use ($trade, &$syncCalled, $orderInfo): void {
            ++$syncCalled;
            if ($actualTrade !== $trade) {
                throw new AssertionFailedError('Unexpected trade in sync use case');
            }
            if ($orderInfo !== $actualOrderInfo) {
                throw new AssertionFailedError('Unexpected order info in sync use case');
            }
        });

        $startChatCalled = 0;
        $startTradeChatScriptUseCase = $this->createStub(StartTradeChatScriptUseCase::class);
        $startTradeChatScriptUseCase->method('execute')->willReturnCallback(function(Trade $actualTrade) use ($trade, &$startChatCalled): void {
            ++$startChatCalled;
            if ($actualTrade !== $trade) {
                throw new AssertionFailedError('Unexpected trade in start chat use case');
            }
        });

        $notificationPublisher = $this->createMock(NotificationPublisherInterface::class);
        $notificationPublisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(static function(SendNotificationDto $dto): bool {
                return 'tradeDiscovered' === $dto->type
                    && 42 === $dto->userId
                    && [
                        'tradeId' => '10',
                        'side' => 'buy',
                        'fiatAmount' => '100.50',
                        'counterpartyName' => 'Bob',
                    ] === $dto->payload;
            }))
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');
        $logger->expects($this->never())->method('error');

        $handler = new TradeDiscoveredMessageHandler(
            $tradeRepository,
            $enrichUseCase,
            $syncUseCase,
            $notificationPublisher,
            $startTradeChatScriptUseCase,
            $logger,
        );

        $handler(new TradeDiscoveredMessage(tradeId: 10, bybitOrderId: 'order-1', fiatAmount: '100.50'));

        self::assertSame(1, $enrichCalled);
        self::assertSame(1, $syncCalled);
        self::assertSame(1, $startChatCalled);
    }

    public function testLogsErrorsButContinuesProcessing(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(10);
        $trade->method('getUfBybitOrderId')->willReturn('order-1');
        $trade->method('getUfSide')->willReturn('buy');
        $trade->method('getUfBuyerUserId')->willReturn(42);
        $trade->method('getUfCounterpartyName')->willReturn('Bob');

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($trade);

        $enrichUseCase = $this->createStub(EnrichTradeFromBybitUseCase::class);
        $enrichUseCase->method('execute')->willThrowException(new \RuntimeException('enrich failed'));

        $syncUseCase = $this->createStub(SyncCounterpartyUseCase::class);

        $startChatCalled = 0;
        $startTradeChatScriptUseCase = $this->createStub(StartTradeChatScriptUseCase::class);
        $startTradeChatScriptUseCase->method('execute')->willReturnCallback(function() use (&$startChatCalled): void {
            ++$startChatCalled;
        });

        $notificationPublisher = $this->createMock(NotificationPublisherInterface::class);
        $notificationPublisher->expects($this->once())->method('publish');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');
        $logger->expects($this->once())->method('info');

        $handler = new TradeDiscoveredMessageHandler(
            $tradeRepository,
            $enrichUseCase,
            $syncUseCase,
            $notificationPublisher,
            $startTradeChatScriptUseCase,
            $logger,
        );

        $handler(new TradeDiscoveredMessage(tradeId: 10, bybitOrderId: 'order-1', fiatAmount: '100.50'));

        self::assertSame(1, $startChatCalled);
    }
}
