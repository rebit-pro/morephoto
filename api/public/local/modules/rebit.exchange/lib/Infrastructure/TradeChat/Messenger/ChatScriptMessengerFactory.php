<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\TradeChat\Messenger;

use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;
use Rebit\Exchange\Application\TradeChat\Message\Handler\ExecuteChatScriptStepMessageHandler;
use Rebit\Share\Infrastructure\Messenger\AbstractMessengerFactory;
use Rebit\Share\Infrastructure\Messenger\MessengerRouteDto;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;

final class ChatScriptMessengerFactory extends AbstractMessengerFactory
{
    /**
     * @return list<MessengerRouteDto>
     */
    protected static function routes(): array
    {
        return [
            new MessengerRouteDto(
                messageClass: ExecuteChatScriptStepMessage::class,
                handlerClass: ExecuteChatScriptStepMessageHandler::class,
                queue: MessengerQueueEnum::CHAT_SCRIPT_STEP,
            ),
        ];
    }
}
