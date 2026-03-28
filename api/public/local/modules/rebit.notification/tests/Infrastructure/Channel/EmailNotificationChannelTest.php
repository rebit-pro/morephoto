<?php

declare(strict_types=1);

namespace Rebit\Notification\Tests\Infrastructure\Channel;

use PHPUnit\Framework\TestCase;
use Rebit\Notification\Domain\Notification\Enum\NotificationTypeEnum;
use Rebit\Notification\Infrastructure\Channel\EmailNotificationChannel;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class EmailNotificationChannelTest extends TestCase
{
    protected function setUp(): void
    {
        \CEvent::$lastSendImmediateCall = null;
        \CEvent::$sendImmediateResult = 'Y';
    }

    public function testSupportsTradeDiscovered(): void
    {
        $channel = new EmailNotificationChannel('s1');

        self::assertTrue($channel->supports(NotificationTypeEnum::TRADE_DISCOVERED));
    }

    public function testDoesNotSupportUnmappedType(): void
    {
        $channel = new EmailNotificationChannel('s1');

        self::assertFalse($channel->supports(NotificationTypeEnum::TRADE_STATUS_CHANGED));
    }

    public function testSendsTradeDiscoveredEmail(): void
    {
        $channel = new EmailNotificationChannel('s1');

        $channel->send(
            NotificationTypeEnum::TRADE_DISCOVERED,
            42,
            [
                'email' => 'trader@example.com',
                'tradeId' => '100',
                'side' => 'buy',
                'fiatAmount' => '50000.00',
                'counterpartyName' => 'CryptoKing',
            ],
        );

        self::assertNotNull(\CEvent::$lastSendImmediateCall);
        self::assertSame('REBIT_NOTIFICATION_TRADE_DISCOVERED', \CEvent::$lastSendImmediateCall['eventName']);
        self::assertSame('s1', \CEvent::$lastSendImmediateCall['siteId']);
        self::assertSame('trader@example.com', \CEvent::$lastSendImmediateCall['fields']['EMAIL_TO']);
        self::assertSame('100', \CEvent::$lastSendImmediateCall['fields']['TRADE_ID']);
        self::assertSame('buy', \CEvent::$lastSendImmediateCall['fields']['SIDE']);
        self::assertSame('50000.00', \CEvent::$lastSendImmediateCall['fields']['FIAT_AMOUNT']);
        self::assertSame('CryptoKing', \CEvent::$lastSendImmediateCall['fields']['COUNTERPARTY_NAME']);
    }

    public function testThrowsWhenEmailMissing(): void
    {
        $channel = new EmailNotificationChannel('s1');

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(422);

        $channel->send(
            NotificationTypeEnum::TRADE_DISCOVERED,
            42,
            ['tradeId' => '100'],
        );
    }

    public function testThrowsWhenBitrixSendFails(): void
    {
        \CEvent::$sendImmediateResult = false;

        $channel = new EmailNotificationChannel('s1');

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(502);

        $channel->send(
            NotificationTypeEnum::TRADE_DISCOVERED,
            42,
            ['email' => 'trader@example.com', 'tradeId' => '100'],
        );
    }
}
