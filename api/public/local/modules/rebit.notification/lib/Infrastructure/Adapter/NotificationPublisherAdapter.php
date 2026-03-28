<?php

declare(strict_types=1);

namespace Rebit\Notification\Infrastructure\Adapter;

use Rebit\Notification\Application\Notification\Message\SendNotificationMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;

/**
 * Адаптер: преобразует межмодульный DTO в сообщение очереди и публикует.
 */
final readonly class NotificationPublisherAdapter implements NotificationPublisherInterface
{
    public function __construct(
        private MessagePublisherInterface $publisher,
    ) {}

    public function publish(SendNotificationDto $dto): void
    {
        $this->publisher->dispatch(
            new SendNotificationMessage(
                type: $dto->type,
                userId: $dto->userId,
                payload: $dto->payload,
            ),
        );
    }
}
