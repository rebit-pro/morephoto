<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Notification\Application\Lead\Port\LeadNotifierInterface;
use Rebit\Notification\Application\Lead\UseCase\SubmitLeadUseCase;
use Rebit\Notification\Infrastructure\Lead\TelegramLeadNotifier;
use Rebit\Notification\Infrastructure\Lead\UploadedFileValidator;
use Rebit\Notification\Presentation\Controller\LeadController;
use Rebit\Share\Infrastructure\Telegram\TelegramBotApiClient;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

// Лимит размера файла ТЗ (МБ). Дефолт 15; согласован с PHP upload_max_filesize.
$leadMaxFileMb = (int)(getenv('REBIT_NOTIFICATION_LEAD_MAX_FILE_MB') ?: 15);
if ($leadMaxFileMb <= 0) {
    $leadMaxFileMb = 15;
}

return [
    LeadNotifierInterface::class => [
        'constructor' => static function(): LeadNotifierInterface {
            return new TelegramLeadNotifier(
                Log::channel(LogChannelEnum::notification),
                ServiceLocator::getInstance()->get(TelegramBotApiClient::class),
                (string)(getenv('REBIT_NOTIFICATION_TELEGRAM_CHAT_ID') ?: ''),
            );
        },
    ],

    UploadedFileValidator::class => [
        'constructor' => static function() use ($leadMaxFileMb): UploadedFileValidator {
            return new UploadedFileValidator($leadMaxFileMb * 1024 * 1024);
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
            ServiceLocator::getInstance()->get(UploadedFileValidator::class),
        ],
    ],
];
