<?php

declare(strict_types=1);

namespace Rebit\Notification\Infrastructure\Channel;

use Rebit\Notification\Application\Notification\Port\NotificationChannelInterface;
use Rebit\Notification\Domain\Notification\Enum\NotificationTypeEnum;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Канал email-уведомлений через Bitrix mail events.
 *
 * Маппинг: NotificationTypeEnum → код почтового события Bitrix.
 */
final readonly class EmailNotificationChannel implements NotificationChannelInterface
{
    /** @var array<string, string> type value => Bitrix event code */
    private const array EVENT_MAP = [
        'tradeDiscovered' => 'REBIT_NOTIFICATION_TRADE_DISCOVERED',
    ];

    public function __construct(
        private string $siteId,
    ) {}

    public function supports(NotificationTypeEnum $type): bool
    {
        return isset(self::EVENT_MAP[$type->value]);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws HttpException
     */
    public function send(NotificationTypeEnum $type, int $userId, array $payload): void
    {
        $eventCode = self::EVENT_MAP[$type->value] ?? null;

        if (null === $eventCode) {
            return;
        }

        $email = (string)($payload['email'] ?? '');

        if ('' === $email) {
            throw new HttpException('Email получателя не указан для уведомления ' . $type->value, 422);
        }

        $fields = $this->buildFields($type, $email, $payload);

        $result = \CEvent::SendImmediate($eventCode, $this->siteId, $fields);

        if (false === $result || 'Y' !== $result) {
            throw new HttpException(
                'Не удалось отправить email-уведомление: ' . $type->value,
                502,
            );
        }
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, string>
     */
    private function buildFields(NotificationTypeEnum $type, string $email, array $payload): array
    {
        return match ($type) {
            NotificationTypeEnum::TRADE_DISCOVERED => [
                'EMAIL_TO' => $email,
                'TRADE_ID' => (string)($payload['tradeId'] ?? ''),
                'SIDE' => (string)($payload['side'] ?? ''),
                'FIAT_AMOUNT' => (string)($payload['fiatAmount'] ?? ''),
                'COUNTERPARTY_NAME' => (string)($payload['counterpartyName'] ?? ''),
            ],
            default => [
                'EMAIL_TO' => $email,
            ],
        };
    }
}
