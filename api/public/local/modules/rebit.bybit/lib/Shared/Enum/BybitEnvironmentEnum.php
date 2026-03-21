<?php

declare(strict_types=1);

namespace Rebit\Bybit\Shared\Enum;

enum BybitEnvironmentEnum: string
{
    case Testnet = 'testnet';
    case Mainnet = 'mainnet';

    public function baseUrl(): string
    {
        return match ($this) {
            self::Testnet => (string) getenv('BYBIT_TESTNET_BASE_URL'),
            self::Mainnet => (string) getenv('BYBIT_MAINNET_BASE_URL'),
        };
    }
}
