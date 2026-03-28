<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Notification;

use Psr\Log\LoggerInterface;
use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;

/**
 * Null-реализация publisher'а уведомлений.
 *
 * Используется как fallback, если модуль rebit.notification не установлен.
 * Логирует попытку отправки и молча пропускает.
 */
final readonly class NullNotificationPublisher implements NotificationPublisherInterface
{
    public function __construct(
        private ?LoggerInterface $logger = null,
    ) {}

    public function publish(SendNotificationDto $dto): void
    {
        $this->logger?->debug('Уведомление проигнорировано (NullPublisher)', [
            'type' => $dto->type,
            'userId' => $dto->userId,
        ]);
    }
}
