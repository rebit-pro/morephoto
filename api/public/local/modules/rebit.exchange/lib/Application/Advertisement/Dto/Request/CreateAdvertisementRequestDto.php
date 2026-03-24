<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final class CreateAdvertisementRequestDto implements RequestDtoInterface
{
    /**
     * @param array<int, string> $paymentMethodIds
     * @param array<string, string> $tradingPreferenceSet
     */
    public function __construct(
        public readonly int $currencyPairId,
        public readonly string $side,
        public readonly string $priceType,
        public readonly string $price,
        public readonly ?string $premium,
        public readonly string $quantity,
        public readonly string $minAmount,
        public readonly string $maxAmount,
        public readonly array $paymentMethodIds,
        public readonly int $paymentPeriod,
        public readonly string $conditions = '',
        public readonly ?int $chatScriptId = null,
        public readonly array $tradingPreferenceSet = [],
    ) {}
}
