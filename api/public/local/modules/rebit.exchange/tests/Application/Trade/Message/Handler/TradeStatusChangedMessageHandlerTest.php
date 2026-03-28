<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Trade\Message\Handler;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Message\Handler\TradeStatusChangedMessageHandler;
use Rebit\Exchange\Application\Trade\Message\TradeStatusChangedMessage;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Application\Contract\Messenger\AbstractMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Balance\Message\SyncBalanceMessage;

/**
 * @internal
 */
final class TradeStatusChangedMessageHandlerTest extends TestCase
{
    /**
     * @throws RepositoryException
     */
    public function testLogsWarningWhenTradeNotFound(): void
    {
        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn(null);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $handler = new TradeStatusChangedMessageHandler(
            $tradeRepository,
            $this->createStub(ChatScriptExecutionRepository::class),
            $this->createStub(MessagePublisherInterface::class),
            $this->createStub(NotificationPublisherInterface::class),
            $logger,
        );

        $handler(new TradeStatusChangedMessage(tradeId: 10, oldStatus: 'pending_payment', newStatus: 'completed'));
    }

    public function testCancelsPendingExecutionsAndPublishesBalanceSync(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(10);
        $trade->method('getUfSide')->willReturn('buy');
        $trade->method('getUfBuyerUserId')->willReturn(42);
        $trade->method('getUfCounterpartyName')->willReturn('Bob');

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($trade);

        $cancelCalled = 0;
        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);
        $executionRepository->method('cancelByTradeId')->willReturnCallback(function(int $tradeId) use (&$cancelCalled): void {
            ++$cancelCalled;
            if (10 !== $tradeId) {
                throw new AssertionFailedError('Unexpected tradeId in cancelByTradeId');
            }
        });

        $balancePublisher = $this->createMock(MessagePublisherInterface::class);
        $balancePublisher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static function(AbstractMessage $message): bool {
                return $message instanceof SyncBalanceMessage
                    && 42 === $message->userId;
            }))
        ;

        $notificationPublisher = $this->createMock(NotificationPublisherInterface::class);
        $notificationPublisher
            ->expects($this->once())
            ->method('publish')
            ->with($this->callback(static function(SendNotificationDto $dto): bool {
                return 'tradeStatusChanged' === $dto->type
                    && 42 === $dto->userId
                    && 'completed' === $dto->payload['newStatus'];
            }))
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->atLeastOnce())->method('info');

        $handler = new TradeStatusChangedMessageHandler(
            $tradeRepository,
            $executionRepository,
            $balancePublisher,
            $notificationPublisher,
            $logger,
        );

        $handler(new TradeStatusChangedMessage(tradeId: 10, oldStatus: 'pending_payment', newStatus: 'completed'));

        self::assertSame(1, $cancelCalled);
    }

    public function testLogsErrorsWhenPublishersFailButDoesNotThrow(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(10);
        $trade->method('getUfSide')->willReturn('buy');
        $trade->method('getUfBuyerUserId')->willReturn(42);
        $trade->method('getUfCounterpartyName')->willReturn('Bob');

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($trade);

        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);

        $balancePublisher = $this->createMock(MessagePublisherInterface::class);
        $balancePublisher->expects($this->once())->method('dispatch')->willThrowException(new \RuntimeException('queue down'));

        $notificationPublisher = $this->createMock(NotificationPublisherInterface::class);
        $notificationPublisher->expects($this->once())->method('publish')->willThrowException(new \RuntimeException('notify down'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))->method('error');
        $logger->expects($this->once())->method('info');

        $handler = new TradeStatusChangedMessageHandler(
            $tradeRepository,
            $executionRepository,
            $balancePublisher,
            $notificationPublisher,
            $logger,
        );

        $handler(new TradeStatusChangedMessage(tradeId: 10, oldStatus: 'pending_payment', newStatus: 'completed'));
    }
}
