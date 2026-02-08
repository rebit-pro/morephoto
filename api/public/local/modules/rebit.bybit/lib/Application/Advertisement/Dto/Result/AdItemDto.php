<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Result;

final class AdItemDto
{
    /**
     * @param string[] $payments
     * @param string[] $authTag
     */
    public function __construct(
        public readonly string $id,
        public readonly string $nickName,
        public readonly string $price,
        public readonly string $lastQuantity,
        public readonly string $minAmount,
        public readonly string $maxAmount,
        public readonly array $payments,
        public readonly string $recentOrderNum,
        public readonly string $recentExecuteRate,
        public readonly bool $isOnline,
        public readonly array $authTag,
        public readonly int $paymentPeriod,
    ) {}
}

