<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class OrderBookEntryResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, string> $paymentMethods
     */
    public function __construct(
        public int $id,
        public string $bybitOrderId,
        public string $side,
        public float $price,
        public float $amount,
        public float $minLimit,
        public float $maxLimit,
        public string $username,
        public float $counterpartyRating,
        public int $completedTrades,
        public float $completionRate,
        public array $paymentMethods,
        public int $paymentTimeLimit,
    ) {}
}
