<?php

declare(strict_types=1);

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/currency.php',
            require __DIR__ . '/di/payment-method.php',
            require __DIR__ . '/di/chat-script.php',
            require __DIR__ . '/di/order-book.php',
            require __DIR__ . '/di/advertisement.php',
            require __DIR__ . '/di/trade.php',
            require __DIR__ . '/di/trade-chat.php',
        ),
        'readonly' => true,
    ],
];
