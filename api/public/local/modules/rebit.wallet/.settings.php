<?php

declare(strict_types=1);

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/balance.php',
            require __DIR__ . '/di/transaction.php',
        ),
        'readonly' => true,
    ],
];
