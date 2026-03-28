<?php

declare(strict_types=1);

use Rebit\Exchange\Presentation\Command\CleanStaleOrdersCommand;
use Rebit\Exchange\Presentation\Command\ExecuteChatScriptsCommand;
use Rebit\Exchange\Presentation\Command\SyncOrderBookCommand;
use Rebit\Exchange\Presentation\Command\SyncTradeHistoryCommand;
use Rebit\Exchange\Presentation\Command\SyncTradesCommand;
use Rebit\Exchange\Presentation\Command\Trade\TestTradeEventCommand;
use Rebit\Exchange\Presentation\Command\Trade\TradeEventConsumerCommand;
use Rebit\Exchange\Presentation\Command\TradeChat\ChatScriptStepConsumerCommand;
use Rebit\Exchange\Presentation\Command\TradeChat\TestChatScriptStepCommand;

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/Messenger.php',
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
    'console' => [
        'value' => [
            'commands' => [
                SyncOrderBookCommand::class,
                SyncTradesCommand::class,
                SyncTradeHistoryCommand::class,
                CleanStaleOrdersCommand::class,
                ExecuteChatScriptsCommand::class,
                TradeEventConsumerCommand::class,
                TestTradeEventCommand::class,
                ChatScriptStepConsumerCommand::class,
                TestChatScriptStepCommand::class,
            ],
        ],
        'readonly' => true,
    ],
];
