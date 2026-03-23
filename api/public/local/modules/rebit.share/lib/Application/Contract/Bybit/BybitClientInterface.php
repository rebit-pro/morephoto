<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

/**
 * Контракт для взаимодействия с Bybit API.
 * Реализация в модуле rebit.bybit.
 */
interface BybitClientInterface
{
    /**
     * @param array<string, string> $queryParams
     *
     * @throws BybitApiException
     */
    public function get(
        string $endpoint,
        BybitCredentials $credentials,
        BybitEnvironmentEnum $environment,
        array $queryParams = [],
    ): BybitResponseDto;

    /**
     * @param array<string, mixed> $body
     *
     * @throws BybitApiException
     */
    public function post(
        string $endpoint,
        BybitCredentials $credentials,
        BybitEnvironmentEnum $environment,
        array $body = [],
    ): BybitResponseDto;
}
