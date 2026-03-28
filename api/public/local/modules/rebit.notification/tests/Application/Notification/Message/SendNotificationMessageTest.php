<?php

declare(strict_types=1);

namespace Rebit\Notification\Tests\Application\Notification\Message;

use PHPUnit\Framework\TestCase;
use Rebit\Notification\Application\Notification\Message\SendNotificationMessage;
use Rebit\Notification\Domain\Notification\Enum\NotificationTypeEnum;

/**
 * @internal
 */
final class SendNotificationMessageTest extends TestCase
{
    public function testCreatesMessageWithExpectedProperties(): void
    {
        $message = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['tradeId' => '100'],
        );

        self::assertSame('tradeDiscovered', $message->type);
        self::assertSame(42, $message->userId);
        self::assertSame(['tradeId' => '100'], $message->payload);
        self::assertGreaterThan(0.0, $message->createdAt);
    }

    public function testDeduplicationKeyIncludesTypeUserAndPayload(): void
    {
        $message1 = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['tradeId' => '100'],
        );

        $message2 = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['tradeId' => '100'],
        );

        self::assertSame($message1->getDeduplicationKey(), $message2->getDeduplicationKey());
    }

    public function testDifferentPayloadsProduceDifferentKeys(): void
    {
        $message1 = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['tradeId' => '100'],
        );

        $message2 = new SendNotificationMessage(
            type: NotificationTypeEnum::TRADE_DISCOVERED->value,
            userId: 42,
            payload: ['tradeId' => '200'],
        );

        self::assertNotSame($message1->getDeduplicationKey(), $message2->getDeduplicationKey());
    }
}
