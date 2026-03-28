<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Bybit;

final readonly class BybitUpdateAdvertisementDto
{
    /**
     * @param list<string> $paymentIds
     */
    public function __construct(
        public string $itemId,
        public string $price,
        public string $premium,
        public string $minAmount,
        public string $maxAmount,
        public array $paymentIds,
        public string $remark,
        public BybitTradingPreferenceSetDto $tradingPreferenceSet,
        public string $quantity,
        public string $paymentPeriod,
    ) {}
}
