<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Bybit;

final readonly class BybitAdvertisementItemDto
{
    /**
     * @param list<string> $payments
     */
    public function __construct(
        public string $id,
        public string $userId,
        public string $nickName,
        public string $tokenId,
        public string $currencyId,
        public int $side,
        public int $priceType,
        public string $price,
        public string $premium,
        public string $lastQuantity,
        public string $quantity,
        public string $frozenQuantity,
        public string $executedQuantity,
        public string $minAmount,
        public string $maxAmount,
        public string $remark,
        public int $status,
        public string $createDate,
        public array $payments,
        public string $hiddenReason,
        public BybitTradingPreferenceSetDto $tradingPreferenceSet,
        public string $updateDate,
        public string $feeRate,
        public int $paymentPeriod,
        public string $itemType,
    ) {}
}
