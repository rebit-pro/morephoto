<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Trade\Messenger;

use Rebit\Exchange\Application\Trade\Message\Handler\TradeDiscoveredMessageHandler;
use Rebit\Exchange\Application\Trade\Message\Handler\TradeStatusChangedMessageHandler;
use Rebit\Exchange\Application\Trade\Message\TradeDiscoveredMessage;
use Rebit\Exchange\Application\Trade\Message\TradeStatusChangedMessage;
use Rebit\Share\Infrastructure\Messenger\AbstractMessengerFactory;
use Rebit\Share\Infrastructure\Messenger\MessengerRouteDto;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;

final class ExchangeTradeMessengerFactory extends AbstractMessengerFactory
{
    /**
     * @return list<MessengerRouteDto>
     */
    protected static function routes(): array
    {
        return [
            new MessengerRouteDto(
                messageClass: TradeDiscoveredMessage::class,
                handlerClass: TradeDiscoveredMessageHandler::class,
                queue: MessengerQueueEnum::TRADE_EVENT,
            ),
            new MessengerRouteDto(
                messageClass: TradeStatusChangedMessage::class,
                handlerClass: TradeStatusChangedMessageHandler::class,
                queue: MessengerQueueEnum::TRADE_EVENT,
            ),
        ];
    }
}
