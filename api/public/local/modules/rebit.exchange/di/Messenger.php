<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Infrastructure\Messenger\AmqpConnectionFactory;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;
use Symfony\Component\Messenger\Transport\TransportInterface;

return [
    MessengerQueueEnum::TRADE_EVENT->transportKey() => [
        'constructor' => static fn(): TransportInterface => ServiceLocator::getInstance()
            ->get(AmqpConnectionFactory::class)
            ->create(MessengerQueueEnum::TRADE_EVENT),
    ],
    MessengerQueueEnum::CHAT_SCRIPT_STEP->transportKey() => [
        'constructor' => static fn(): TransportInterface => ServiceLocator::getInstance()
            ->get(AmqpConnectionFactory::class)
            ->create(MessengerQueueEnum::CHAT_SCRIPT_STEP),
    ],
];
