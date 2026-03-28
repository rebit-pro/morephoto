<?php

declare(strict_types=1);

use Rebit\Identity\Presentation\Command\ApiConnection\IdentitySyncConsumerCommand;
use Rebit\Identity\Presentation\Command\ApiConnection\TestIdentitySyncCommand;

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/Messenger.php',
            require __DIR__ . '/di/connection.php',
        ),
        'readonly' => true,
    ],
    'console' => [
        'value' => [
            'commands' => [
                IdentitySyncConsumerCommand::class,
                TestIdentitySyncCommand::class,
            ],
        ],
        'readonly' => true,
    ],
];
