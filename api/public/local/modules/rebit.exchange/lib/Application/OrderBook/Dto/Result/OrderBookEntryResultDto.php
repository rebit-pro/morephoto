<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class OrderBookEntryResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, string> $paymentMethodIds
     */
    public function __construct(
        public int $id,
        public string $bybitOrderId,
        public string $side,
        public float $price,
        public float $quantity,
        public float $minAmount,
        public float $maxAmount,
        public string $counterpartyName,
        public float $counterpartyRating,
        public int $counterpartyTrades,
        public float $counterpartyCompletionRate,
        public array $paymentMethodIds,
        public int $paymentTimeLimit,
    ) {}
}
