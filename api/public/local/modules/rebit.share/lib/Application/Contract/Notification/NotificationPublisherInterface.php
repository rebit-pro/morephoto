<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Notification;

use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;

/**
 * Порт для отправки уведомлений из любого модуля.
 */
interface NotificationPublisherInterface
{
    public function publish(SendNotificationDto $dto): void;
}
