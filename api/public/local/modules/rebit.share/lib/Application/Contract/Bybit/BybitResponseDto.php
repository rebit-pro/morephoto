<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

/**
 * DTO ответа Bybit API.
 */
final readonly class BybitResponseDto
{
    public function __construct(
        public int $retCode,
        public string $retMsg,
        /** @var array[] */
        public array $result,
        /** @var array[] */
        public array $retExtInfo,
        public int $time,
    ) {}
}
