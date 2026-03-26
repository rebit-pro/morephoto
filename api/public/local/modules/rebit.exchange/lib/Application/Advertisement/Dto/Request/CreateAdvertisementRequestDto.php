<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final readonly class CreateAdvertisementRequestDto implements RequestDtoInterface
{
    /**
     * @param array<int, string>    $paymentMethodIds
     * @param array<string, string> $tradingPreferenceSet
     */
    public function __construct(
        public int $currencyPairId,
        public string $side,
        public string $priceType,
        public string $price,
        public ?string $premium,
        public string $quantity,
        public string $minAmount,
        public string $maxAmount,
        public array $paymentMethodIds,
        public int $paymentPeriod,
        public string $conditions = '',
        public ?int $chatScriptId = null,
        public array $tradingPreferenceSet = [],
    ) {}
}
