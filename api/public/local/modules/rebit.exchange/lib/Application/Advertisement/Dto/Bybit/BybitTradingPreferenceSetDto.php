<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Bybit;

final readonly class BybitTradingPreferenceSetDto
{
    public function __construct(
        public string $hasUnPostAd = '',
        public string $isKyc = '',
        public string $isEmail = '',
        public string $isMobile = '',
        public string $hasRegisterTime = '',
        public string $registerTimeThreshold = '',
        public string $orderFinishNumberDay30 = '',
        public string $completeRateDay30 = '',
        public string $nationalLimit = '',
        public string $hasOrderFinishNumberDay30 = '',
        public string $hasCompleteRateDay30 = '',
        public string $hasNationalLimit = '',
    ) {}
}
