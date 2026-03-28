<?php

declare(strict_types=1);

namespace Rebit\Notification\Tests\Application\Notification\Message\Handler;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rebit\Notification\Application\Notification\Message\Handler\SendNotificationMessageHandler;
use Rebit\Notification\Application\Notification\Message\SendNotificationMessage;
use Rebit\Notification\Application\Notification\Port\NotificationChannelInterface;
use Rebit\Notification\Application\Notification\Port\UserEmailResolverInterface;
use Rebit\Notification\Domain\Notification\Enum\NotificationTypeEnum;

/**
 * @internal
 */
final class SendNotificationMessageHandlerTest extends TestCase
{
    private function createEmailResolver(?string $email = 'resolved@example.com'): UserEmailResolverInterface
    {
        $resolver = $this->createStub(UserEmailResolverInterface::class);
        $resolver->method('resolve')->willReturn($email);

        return $resolver;
    }

    public function testDispatchesToSupportedChannel(): void
    {
        $channel = $this->createMock(NotificationChannelInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $channel
            ->expects($this->once())
            ->method('supports')
            ->with(NotificationTypeEnum::TRADE_DISCOVERED)
            ->willReturn(true)
        ;

        $channel
            ->expects($this->once())
            ->method('send')
            ->with(
                NotificationTypeEnum::TRADE_DISCOVERED,
                42,
                ['tradeId' => '100', 'email' => 'user@example.com'],
            )
        ;

        $handler = new SendNotificationMessageHandler(
            [$channel],
            $this->createEmailResolver(),
            $logger,
        );

        $message = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['tradeId' => '100', 'email' => 'user@example.com'],
        );

        $handler($message);
    }

    public function testEnrichesPayloadWithEmailFromResolver(): void
    {
        $channel = $this->createMock(NotificationChannelInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $channel->method('supports')->willReturn(true);

        $channel
            ->expects($this->once())
            ->method('send')
            ->with(
                NotificationTypeEnum::TRADE_DISCOVERED,
                42,
                $this->callback(static fn(array $payload): bool => 'resolved@example.com' === $payload['email']),
            )
        ;

        $handler = new SendNotificationMessageHandler(
            [$channel],
            $this->createEmailResolver('resolved@example.com'),
            $logger,
        );

        $message = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['tradeId' => '100'],
        );

        $handler($message);
    }

    public function testSkipsUnsupportedChannel(): void
    {
        $channel = $this->createMock(NotificationChannelInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $channel
            ->expects($this->once())
            ->method('supports')
            ->willReturn(false)
        ;

        $channel
            ->expects($this->never())
            ->method('send')
        ;

        $handler = new SendNotificationMessageHandler(
            [$channel],
            $this->createEmailResolver(),
            $logger,
        );

        $message = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: [],
        );

        $handler($message);
    }

    public function testLogsWarningOnUnknownType(): void
    {
        $channel = $this->createStub(NotificationChannelInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $logger
            ->expects($this->once())
            ->method('warning')
            ->with('Неизвестный тип уведомления', $this->anything())
        ;

        $handler = new SendNotificationMessageHandler(
            [$channel],
            $this->createEmailResolver(),
            $logger,
        );

        $message = new SendNotificationMessage(
            type: 'unknownType',
            userId: 1,
            payload: [],
        );

        $handler($message);
    }

    public function testRethrowsChannelException(): void
    {
        $channel = $this->createMock(NotificationChannelInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $channel->method('supports')->willReturn(true);
        $channel->method('send')->willThrowException(new \RuntimeException('SMTP down'));

        $handler = new SendNotificationMessageHandler(
            [$channel],
            $this->createEmailResolver(),
            $logger,
        );

        $message = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 1,
            payload: [],
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('SMTP down');

        $handler($message);
    }

    public function testDispatchesToMultipleChannels(): void
    {
        $emailChannel = $this->createMock(NotificationChannelInterface::class);
        $telegramChannel = $this->createMock(NotificationChannelInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $emailChannel->method('supports')->willReturn(true);
        $emailChannel->expects($this->once())->method('send');

        $telegramChannel->method('supports')->willReturn(true);
        $telegramChannel->expects($this->once())->method('send');

        $handler = new SendNotificationMessageHandler(
            [$emailChannel, $telegramChannel],
            $this->createEmailResolver(),
            $logger,
        );

        $message = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['email' => 'user@example.com'],
        );

        $handler($message);
    }
}
