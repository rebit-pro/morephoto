<?php

declare(strict_types=1);

use Rebit\Wallet\Presentation\Command\SyncBalancesCommand;

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/balance.php',
            require __DIR__ . '/di/transaction.php',
        ),
        'readonly' => true,
    ],
    'console' => [
        'value' => [
            'commands' => [
                SyncBalancesCommand::class,
            ],
        ],
        'readonly' => true,
    ],
];
