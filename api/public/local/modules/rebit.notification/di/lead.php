<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Notification\Application\Lead\Port\LeadNotifierInterface;
use Rebit\Notification\Application\Lead\UseCase\SubmitLeadUseCase;
use Rebit\Notification\Infrastructure\Lead\TelegramLeadNotifier;
use Rebit\Notification\Presentation\Controller\LeadController;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    LeadNotifierInterface::class => [
        'constructor' => static function(): LeadNotifierInterface {
            return new TelegramLeadNotifier(
                Log::channel(LogChannelEnum::notification),
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_BOT_TOKEN') ?: ''),
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_CHAT_ID') ?: ''),
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_API_URL') ?: 'https://api.telegram.org'),
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_PROXY') ?: ''),
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
        ],
    ],
];
