<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Result;

use Rebit\Share\Shared\Interface\ResultDtoInterface;

final class CreateAdResultDto implements ResultDtoInterface
{
    public function __construct(
        public readonly string $itemId,
        public readonly string $securityRiskToken,
        public readonly string $riskTokenType,
        public readonly string $riskVersion,
        public readonly bool $needSecurityRisk,
    ) {}
}

