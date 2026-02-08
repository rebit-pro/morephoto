<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Request;

use Rebit\Share\Shared\Interface\RequestDtoInterface;

final class UpdateAdRequestDto implements RequestDtoInterface
{
    /**
     * @param string[] $paymentIds Payment method type ID (len<=5)
     */
    public function __construct(
        /** Advertisement ID */
        public readonly string $id,
        /** 0: fixed rate; 1: floating rate */
        public readonly string $priceType,
        /** Floating ratio with current exchange rate */
        public readonly string $premium,
        /** Price per token, in currency */
        public readonly string $price,
        public readonly string $minAmount,
        public readonly string $maxAmount,
        /** max length: 900 */
        public readonly string $remark,
        public readonly CreateAdTradingPreferenceSetDto $tradingPreferenceSet,
        public readonly array $paymentIds,
        /** MODIFY|ACTIVE */
        public readonly string $actionType,
        public readonly string $quantity,
        /** unit: minutes */
        public readonly string $paymentPeriod,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'priceType' => $this->priceType,
            'premium' => $this->premium,
            'price' => $this->price,
            'minAmount' => $this->minAmount,
            'maxAmount' => $this->maxAmount,
            'remark' => $this->remark,
            'tradingPreferenceSet' => $this->tradingPreferenceSet->toArray(),
            'paymentIds' => array_values($this->paymentIds),
            'actionType' => $this->actionType,
            'quantity' => $this->quantity,
            'paymentPeriod' => $this->paymentPeriod,
        ];
    }
}

