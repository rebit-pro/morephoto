<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Notification\Application\Lead\Port\LeadNotifierInterface;
use Rebit\Notification\Application\Lead\UseCase\SubmitLeadUseCase;
use Rebit\Notification\Infrastructure\Lead\TelegramLeadNotifier;
use Rebit\Notification\Presentation\Controller\LeadController;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClientFactory;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    LeadNotifierInterface::class => [
        'constructor' => static function(): LeadNotifierInterface {
            $logger = Log::channel(LogChannelEnum::notification);

            return new TelegramLeadNotifier(
                RebitHttpClientFactory::create($logger),
                $logger,
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_BOT_TOKEN') ?: ''),
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_CHAT_ID') ?: ''),
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_API_URL') ?: 'https://api.telegram.org'),
            );
        },
    ],

    SubmitLeadUseCase::class => [
        'className' => SubmitLeadUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(LeadNotifierInterface::class),
        ],
    ],

    LeadController::class => [
        'className' => LeadController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(SubmitLeadUseCase::class),
            (string)(getenv('REBIT_NOTIFICATION_LEAD_ALLOWED_ORIGIN') ?: 'https://rebit-pro.ru'),
        ],
    ],
];
