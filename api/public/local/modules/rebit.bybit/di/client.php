<?php

declare(strict_types=1);

use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Bybit\Infrastructure\Client\BybitApiClientFactory;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    BybitClientInterface::class => [
        'constructor' => static function (): BybitClientInterface {
            return BybitApiClientFactory::create(
                Log::channel(LogChannelEnum::bybit),
            );
        },
    ],
];
