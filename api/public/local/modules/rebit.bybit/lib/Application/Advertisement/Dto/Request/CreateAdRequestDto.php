<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Request;

use Rebit\Share\Shared\Interface\RequestDtoInterface;

final class CreateAdRequestDto implements RequestDtoInterface
{
    /**
     * @param string[] $paymentIds Payment method type ID (len<=5)
     */
    public function __construct(
        public readonly string $tokenId,
        public readonly string $currencyId,
        /** 0: buy; 1: sell */
        public readonly string $side,
        /** 0: fixed rate; 1: floating rate */
        public readonly string $priceType,
        public readonly string $premium,
        public readonly string $price,
        public readonly string $minAmount,
        public readonly string $maxAmount,
        public readonly string $remark,
        public readonly CreateAdTradingPreferenceSetDto $tradingPreferenceSet,
        public readonly array $paymentIds,
        public readonly string $quantity,
        /** unit: minutes */
        public readonly string $paymentPeriod,
        /** ORIGIN|BULK */
        public readonly string $itemType,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'tokenId' => $this->tokenId,
            'currencyId' => $this->currencyId,
            'side' => $this->side,
            'priceType' => $this->priceType,
            'premium' => $this->premium,
            'price' => $this->price,
            'minAmount' => $this->minAmount,
            'maxAmount' => $this->maxAmount,
            'remark' => $this->remark,
            'tradingPreferenceSet' => $this->tradingPreferenceSet->toArray(),
            'paymentIds' => array_values($this->paymentIds),
            'quantity' => $this->quantity,
            'paymentPeriod' => $this->paymentPeriod,
            'itemType' => $this->itemType,
        ];
    }
}

