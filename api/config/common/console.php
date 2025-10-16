<?php

declare(strict_types=1);

use api\src\Console\HelloCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                HelloCommand::class,
            ]
        ]
    ],
];