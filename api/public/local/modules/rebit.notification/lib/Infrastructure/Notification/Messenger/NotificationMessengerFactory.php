<?php

declare(strict_types=1);

namespace Rebit\Notification\Infrastructure\Notification\Messenger;

use Rebit\Notification\Application\Notification\Message\Handler\SendNotificationMessageHandler;
use Rebit\Notification\Application\Notification\Message\SendNotificationMessage;
use Rebit\Share\Infrastructure\Messenger\AbstractMessengerFactory;
use Rebit\Share\Infrastructure\Messenger\MessengerRouteDto;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;

final class NotificationMessengerFactory extends AbstractMessengerFactory
{
    /**
     * @return list<MessengerRouteDto>
     */
    protected static function routes(): array
    {
        return [
            new MessengerRouteDto(
                messageClass: SendNotificationMessage::class,
                handlerClass: SendNotificationMessageHandler::class,
                queue: MessengerQueueEnum::NOTIFICATION,
            ),
        ];
    }
}
