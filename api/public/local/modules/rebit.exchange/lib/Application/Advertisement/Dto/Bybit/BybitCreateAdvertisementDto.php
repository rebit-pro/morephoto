<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Bybit;

final readonly class BybitCreateAdvertisementDto
{
    /**
     * @param list<string> $paymentIds
     */
    public function __construct(
        public string $tokenId,
        public string $currencyId,
        public string $side,
        public string $priceType,
        public string $premium,
        public string $price,
        public string $minAmount,
        public string $maxAmount,
        public array $paymentIds,
        public string $remark,
        public BybitTradingPreferenceSetDto $tradingPreferenceSet,
        public string $quantity,
        public string $paymentPeriod,
        public string $itemType,
    ) {}
}
