<?php

declare(strict_types=1);

use Rebit\Wallet\Presentation\Command\SyncBalancesCommand;
use Rebit\Wallet\Presentation\Command\Balance\BalanceSyncConsumerCommand;
use Rebit\Wallet\Presentation\Command\Balance\TestBalanceSyncCommand;

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/Messenger.php',
            require __DIR__ . '/di/balance.php',
            require __DIR__ . '/di/transaction.php',
            require __DIR__ . '/di/report.php',
        ),
        'readonly' => true,
    ],
    'console' => [
        'value' => [
            'commands' => [
                SyncBalancesCommand::class,
                BalanceSyncConsumerCommand::class,
                TestBalanceSyncCommand::class,
            ],
        ],
        'readonly' => true,
    ],
];
