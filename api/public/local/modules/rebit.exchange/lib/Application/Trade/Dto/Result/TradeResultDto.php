<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class TradeResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public string $bybitOrderId,
        public int $bybitStatus,
        public string $side,
        public float $price,
        public float $quantity,
        public float $fiatAmount,
        public float $fee,
        public string $status,
        public string $counterpartyName,
        public int $currencyPairId,
        public ?int $advertisementId,
        public ?string $paymentDeadline,
        public ?string $paidAt,
        public ?string $completedAt,
        public ?string $cancelledAt,
        public ?string $cancelReason,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}
}
