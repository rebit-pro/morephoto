<?php

declare(strict_types=1);

namespace Rebit\Notification\Application\Notification\Port;

use Rebit\Notification\Domain\Notification\Enum\NotificationTypeEnum;

/**
 * Порт канала доставки уведомлений.
 *
 * Реализации: EmailNotificationChannel, (TelegramNotificationChannel, ...).
 */
interface NotificationChannelInterface
{
    /**
     * Поддерживает ли канал данный тип уведомления.
     */
    public function supports(NotificationTypeEnum $type): bool;

    /**
     * Отправляет уведомление.
     *
     * @param array<string, mixed> $payload данные уведомления (содержимое зависит от типа)
     */
    public function send(NotificationTypeEnum $type, int $userId, array $payload): void;
}
