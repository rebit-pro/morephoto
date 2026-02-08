<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Request;

final readonly class CreateAdTradingPreferenceSetDto
{
    public function __construct(
        /** Whether the counterparty must not have posted any advertisements. 0: not required; 1: required */
        public string $hasUnPostAd = '0',
        /** Is it necessary for the counterparty to complete identity authentication? 0: not required; 1: required */
        public string $isKyc = '0',
        /** Is it necessary for the counterparty to bind an email address? 0: not required; 1: required */
        public string $isEmail = '0',
        /** Is it necessary for the counterparty to bind a mobile number? 0: not required; 1: required */
        public string $isMobile = '0',
        /** Is the registration time required to be no less than {} days? 0: not required; 1: required */
        public string $hasRegisterTime = '0',
        /** Registration time threshold(Unit: Day) */
        public string $registerTimeThreshold = '0',
        /** Limit on number of completed orders in the last 30 days */
        public string $orderFinishNumberDay30 = '0',
        /** Completion rate in the last 30 days */
        public string $completeRateDay30 = '0',
        /** KYC restricted countries. Format: three-letter ISO country code */
        public string $nationalLimit = '',
        /** Is it necessary to have no less than {} orders in the last 30 days? 0: not required; 1: required */
        public string $hasOrderFinishNumberDay30 = '0',
        /** Is it necessary to have a completion rate of no less than {} in the last 30 days? 0: not required; 1: required */
        public string $hasCompleteRateDay30 = '0',
        /** Whether or not KYC restricted countries list enabled; 0: disabled; 1: enabled */
        public string $hasNationalLimit = '0',
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'hasUnPostAd' => $this->hasUnPostAd,
            'isKyc' => $this->isKyc,
            'isEmail' => $this->isEmail,
            'isMobile' => $this->isMobile,
            'hasRegisterTime' => $this->hasRegisterTime,
            'registerTimeThreshold' => $this->registerTimeThreshold,
            'orderFinishNumberDay30' => $this->orderFinishNumberDay30,
            'completeRateDay30' => $this->completeRateDay30,
            'nationalLimit' => $this->nationalLimit,
            'hasOrderFinishNumberDay30' => $this->hasOrderFinishNumberDay30,
            'hasCompleteRateDay30' => $this->hasCompleteRateDay30,
            'hasNationalLimit' => $this->hasNationalLimit,
        ];
    }
}
