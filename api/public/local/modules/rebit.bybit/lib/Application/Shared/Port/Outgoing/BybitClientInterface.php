<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Shared\Port\Outgoing;

use Rebit\Bybit\Application\Shared\Dto\BybitCredentialsDto;
use Rebit\Bybit\Application\Shared\Dto\BybitResponseDto;
use Rebit\Bybit\Infrastructure\Exception\BybitApiException;
use Rebit\Bybit\Shared\Enum\BybitEnvironmentEnum;

interface BybitClientInterface
{
    /**
     * @param array<string, string> $queryParams
     *
     * @throws BybitApiException
     */
    public function get(
        string $endpoint,
        BybitCredentialsDto $credentials,
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
        BybitCredentialsDto $credentials,
        BybitEnvironmentEnum $environment,
        array $body = [],
    ): BybitResponseDto;
}
