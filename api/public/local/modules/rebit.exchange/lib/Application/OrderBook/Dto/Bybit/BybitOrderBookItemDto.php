<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\Dto\Bybit;

final readonly class BybitOrderBookItemDto
{
    /**
     * @param list<string> $payments
     */
    public function __construct(
        public string $id,
        public string $price,
        public string $lastQuantity,
        public string $minAmount,
        public string $maxAmount,
        public string $nickName,
        public float $recentExecuteRate,
        public int $recentOrderNum,
        public array $payments,
        public int $paymentPeriod,
        public int $side,
    ) {}
}
