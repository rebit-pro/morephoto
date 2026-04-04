<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

enum BybitEnvironmentEnum: string
{
    case Testnet = 'testnet';
    case Mainnet = 'mainnet';

    private const string TESTNET_DEFAULT_BASE_URL = 'https://api-testnet.bybit.com';
    private const string MAINNET_DEFAULT_BASE_URL = 'https://api.bybit.com';

    public function baseUrl(): string
    {
        return match ($this) {
            self::Testnet => $this->resolveBaseUrl(
                envName: 'BYBIT_TESTNET_BASE_URL',
                defaultUrl: self::TESTNET_DEFAULT_BASE_URL,
            ),
            self::Mainnet => $this->resolveBaseUrl(
                envName: 'BYBIT_MAINNET_BASE_URL',
                defaultUrl: self::MAINNET_DEFAULT_BASE_URL,
            ),
        };
    }

    private function resolveBaseUrl(string $envName, string $defaultUrl): string
    {
        $baseUrl = trim((string)getenv($envName));

        if ('' === $baseUrl) {
            return $defaultUrl;
        }

        $scheme = parse_url($baseUrl, PHP_URL_SCHEME);

        if (!is_string($scheme) || false === in_array(strtolower($scheme), ['http', 'https'], true)) {
            return $defaultUrl;
        }

        return rtrim($baseUrl, '/');
    }
}
