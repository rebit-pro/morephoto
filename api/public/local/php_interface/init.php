<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../vendor/autoload.php';

use Bitrix\Main\Loader;
use Symfony\Component\Dotenv\Dotenv;

$envPath = __DIR__ . '/../../.env';
if (is_file($envPath)) {
    new Dotenv()
        ->usePutenv(true)
        ->loadEnv($envPath)
    ;
}

if (file_exists(__DIR__ . '/include/dev.php')) {
    require_once __DIR__ . '/include/dev.php';
}

Loader::includeModule('rebit.share');
Loader::includeModule('rebit.auth');
Loader::includeModule('rebit.bybit');
Loader::includeModule('rebit.identity');
Loader::includeModule('rebit.wallet');
Loader::includeModule('rebit.exchange');
