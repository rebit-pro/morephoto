<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

/**
 * DTO для активного подключения к Bybit.
 */
final readonly class BybitConnectionDto
{
    public function __construct(
        public BybitCredentials $credentials,
        public BybitEnvironmentEnum $environment,
    ) {}
}
