<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

/**
 * DTO ответа Bybit API.
 */
final readonly class BybitResponseDto
{
    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $retExtInfo
     */
    public function __construct(
        public int $retCode,
        public string $retMsg,
        public array $result,
        public array $retExtInfo,
        public int $time,
    ) {}
}
