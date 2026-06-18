<?php

declare(strict_types=1);

use Rebit\Notification\Presentation\Command\Notification\NotificationConsumerCommand;
use Rebit\Notification\Presentation\Command\Notification\TestSendNotificationCommand;

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/Messenger.php',
            require __DIR__ . '/di/notification.php',
            require __DIR__ . '/di/lead.php',
        ),
        'readonly' => true,
    ],
    'console' => [
        'value' => [
            'commands' => [
                NotificationConsumerCommand::class,
                TestSendNotificationCommand::class,
            ],
        ],
        'readonly' => true,
    ],
];
