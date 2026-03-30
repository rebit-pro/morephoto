<?php

declare(strict_types=1);

namespace Rebit\Share\Tests\Application\Contract\Bybit;

use PHPUnit\Framework\TestCase;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;

/**
 * @internal
 */
final class BybitEnvironmentEnumTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('BYBIT_TESTNET_BASE_URL');
        putenv('BYBIT_MAINNET_BASE_URL');
        unset($_ENV['BYBIT_TESTNET_BASE_URL'], $_ENV['BYBIT_MAINNET_BASE_URL'], $_SERVER['BYBIT_TESTNET_BASE_URL'], $_SERVER['BYBIT_MAINNET_BASE_URL']);
    }

    public function testReturnsDefaultTestnetUrlWhenEnvIsMissing(): void
    {
        self::assertSame(
            'https://api-testnet.bybit.com',
            BybitEnvironmentEnum::Testnet->baseUrl(),
        );
    }

    public function testReturnsDefaultMainnetUrlWhenEnvHasUnsupportedScheme(): void
    {
        putenv('BYBIT_MAINNET_BASE_URL=ftp://api.bybit.com');

        self::assertSame(
            'https://api.bybit.com',
            BybitEnvironmentEnum::Mainnet->baseUrl(),
        );
    }

    public function testTrimsTrailingSlashFromCustomUrl(): void
    {
        putenv('BYBIT_TESTNET_BASE_URL=https://custom-testnet.bybit.local///');

        self::assertSame(
            'https://custom-testnet.bybit.local',
            BybitEnvironmentEnum::Testnet->baseUrl(),
        );
    }
}
