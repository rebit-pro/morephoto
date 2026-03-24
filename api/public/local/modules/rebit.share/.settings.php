<?php

declare(strict_types=1);

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/Layers/Infrastructure.php',
            require __DIR__ . '/di/file.php',
        ),
    ],
];
