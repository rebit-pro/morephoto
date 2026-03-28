<?php

declare(strict_types=1);

namespace Rebit\Identity\Infrastructure\ApiConnection\Messenger;

use Rebit\Identity\Application\ApiConnection\Message\Handler\SyncIdentityMessageHandler;
use Rebit\Identity\Application\ApiConnection\Message\SyncIdentityMessage;
use Rebit\Share\Infrastructure\Messenger\AbstractMessengerFactory;
use Rebit\Share\Infrastructure\Messenger\MessengerRouteDto;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;

final class IdentityMessengerFactory extends AbstractMessengerFactory
{
    /**
     * @return list<MessengerRouteDto>
     */
    protected static function routes(): array
    {
        return [
            new MessengerRouteDto(
                messageClass: SyncIdentityMessage::class,
                handlerClass: SyncIdentityMessageHandler::class,
                queue: MessengerQueueEnum::IDENTITY_SYNC,
            ),
        ];
    }
}
