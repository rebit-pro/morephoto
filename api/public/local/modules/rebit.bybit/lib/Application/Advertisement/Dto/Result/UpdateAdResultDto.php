<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Result;

use Rebit\Share\Shared\Interface\ResultDtoInterface;

final readonly class UpdateAdResultDto implements ResultDtoInterface
{
    public function __construct(
        public string $securityRiskToken,
        public string $riskTokenType,
        public string $riskVersion,
        public bool $needSecurityRisk,
    ) {}
}
