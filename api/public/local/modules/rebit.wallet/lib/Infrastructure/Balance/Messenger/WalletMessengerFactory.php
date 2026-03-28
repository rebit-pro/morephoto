<?php

declare(strict_types=1);

namespace Rebit\Wallet\Infrastructure\Balance\Messenger;

use Rebit\Share\Application\Contract\Wallet\Message\SyncBalanceMessage;
use Rebit\Share\Infrastructure\Messenger\AbstractMessengerFactory;
use Rebit\Share\Infrastructure\Messenger\MessengerRouteDto;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;
use Rebit\Wallet\Application\Balance\Message\Handler\SyncBalanceMessageHandler;

final class WalletMessengerFactory extends AbstractMessengerFactory
{
    /**
     * @return list<MessengerRouteDto>
     */
    protected static function routes(): array
    {
        return [
            new MessengerRouteDto(
                messageClass: SyncBalanceMessage::class,
                handlerClass: SyncBalanceMessageHandler::class,
                queue: MessengerQueueEnum::BALANCE_SYNC,
            ),
        ];
    }
}
