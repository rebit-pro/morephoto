<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class AdvertisementResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, string> $paymentMethodIds
     */
    public function __construct(
        public int $id,
        public string $bybitAdId,
        public int $currencyPairId,
        public string $side,
        public string $priceType,
        public float $price,
        public float $premium,
        public float $quantity,
        public float $quantityRemaining,
        public float $minAmount,
        public float $maxAmount,
        public array $paymentMethodIds,
        public int $paymentPeriod,
        public float $feeRate,
        public string $conditions,
        public ?int $chatScriptId,
        public string $status,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}
}
