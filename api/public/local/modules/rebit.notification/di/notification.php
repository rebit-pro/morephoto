<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Notification\Application\Notification\Message\Handler\SendNotificationMessageHandler;
use Rebit\Notification\Application\Notification\Port\UserEmailResolverInterface;
use Rebit\Notification\Application\Notification\UseCase\ConsumeNotificationsUseCase;
use Rebit\Notification\Infrastructure\Adapter\BitrixUserEmailResolver;
use Rebit\Notification\Infrastructure\Adapter\NotificationPublisherAdapter;
use Rebit\Notification\Infrastructure\Channel\EmailNotificationChannel;
use Rebit\Notification\Infrastructure\Notification\Messenger\NotificationMessengerFactory;
use Rebit\Notification\Presentation\Command\Notification\NotificationConsumerCommand;
use Rebit\Notification\Presentation\Command\Notification\TestSendNotificationCommand;
use Rebit\Share\Application\Contract\Messenger\MessageConsumerRunnerInterface;
use Rebit\Share\Application\Contract\Messenger\MessageTransportFactoryInterface;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    /**
     * Каналы доставки уведомлений.
     *
     * Чтобы добавить новый канал (например, Telegram):
     * 1. Реализуйте NotificationChannelInterface
     * 2. Добавьте сюда новый сервис
     * 3. Добавьте его в массив channels в SendNotificationMessageHandler::constructorParams
     */
    EmailNotificationChannel::class => [
        'constructor' => static fn(): EmailNotificationChannel => new EmailNotificationChannel(
            siteId: (string)(getenv('REBIT_AUTH_MAIL_EVENT_SITE_ID') ?: 's1'),
        ),
    ],

    UserEmailResolverInterface::class => [
        'constructor' => static fn(): UserEmailResolverInterface => new BitrixUserEmailResolver(),
    ],

    SendNotificationMessageHandler::class => [
        'className' => SendNotificationMessageHandler::class,
        'constructorParams' => static fn(): array => [
            [
                ServiceLocator::getInstance()->get(EmailNotificationChannel::class),
                // ServiceLocator::getInstance()->get(TelegramNotificationChannel::class),
            ],
            ServiceLocator::getInstance()->get(UserEmailResolverInterface::class),
            Log::channel(LogChannelEnum::notification),
        ],
    ],

    NotificationPublisherInterface::class => [
        'constructor' => static fn(): NotificationPublisherInterface => new NotificationPublisherAdapter(
            NotificationMessengerFactory::createPublisher(ServiceLocator::getInstance()),
        ),
    ],

    ConsumeNotificationsUseCase::class => [
        'className' => ConsumeNotificationsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(MessageConsumerRunnerInterface::class),
            ServiceLocator::getInstance()->get(MessageTransportFactoryInterface::class),
            NotificationMessengerFactory::createBus(ServiceLocator::getInstance()),
        ],
    ],

    NotificationConsumerCommand::class => [
        'className' => NotificationConsumerCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumeNotificationsUseCase::class),
        ],
    ],

    TestSendNotificationCommand::class => [
        'className' => TestSendNotificationCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(NotificationPublisherInterface::class),
        ],
    ],
];
