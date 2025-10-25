<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Bitrix\Main\Loader;

$envPath = __DIR__ . '/../../.env';
if (is_file($envPath)) {
    (new Dotenv())
        ->usePutenv(true)
        ->loadEnv($envPath)
    ;
}

if (file_exists(__DIR__ . '/include/dev.php')) {
    require_once __DIR__ . '/include/dev.php';
}
Loader::includeModule('rebit.dev');
Loader::includeModule('rebit.share');
Loader::includeModule('rebit.photoorder');
