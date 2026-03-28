<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Infrastructure\Messenger\AmqpConnectionFactory;
use Rebit\Share\Infrastructure\Messenger\BitrixDedupCache;
use Rebit\Share\Infrastructure\Messenger\ConsumerRunner;
use Rebit\Share\Infrastructure\Messenger\ConsumerRunnerInterface;
use Rebit\Share\Infrastructure\Messenger\DedupCacheInterface;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;
use Rebit\Share\Shared\Facade\Log;
use Symfony\Component\Messenger\Retry\MultiplierRetryStrategy;

return [
    AmqpConnectionFactory::class => [
        'className' => AmqpConnectionFactory::class,
        'constructorParams' => static fn(): array => [
            (string)getenv('MESSENGER_TRANSPORT_DSN'),
        ],
    ],
    ConsumerRunnerInterface::class => [
        'constructor' => static function(): ConsumerRunnerInterface {
            $locator = ServiceLocator::getInstance();

            return new ConsumerRunner(
                Log::channel(LogChannelEnum::cli),
                new MultiplierRetryStrategy(maxRetries: 3, delayMilliseconds: 1000, multiplier: 2),
                $locator->get(AmqpConnectionFactory::class)->create(MessengerQueueEnum::FAILED),
            );
        },
    ],
    DedupCacheInterface::class => [
        'className' => BitrixDedupCache::class,
    ],
];
