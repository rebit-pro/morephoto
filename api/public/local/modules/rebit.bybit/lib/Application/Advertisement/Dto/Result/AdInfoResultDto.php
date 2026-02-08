<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Result;

use Rebit\Share\Shared\Interface\ResultDtoInterface;

final class AdInfoResultDto implements ResultDtoInterface
{
    /**
     * @param string[] $payments
     */
    public function __construct(
        public readonly string $id,
        public readonly string $accountId,
        public readonly string $userId,
        public readonly string $nickName,
        public readonly string $tokenId,
        public readonly string $currencyId,
        public readonly int $side,
        public readonly int $priceType,
        public readonly string $price,
        public readonly string $premium,
        public readonly string $lastQuantity,
        public readonly string $quantity,
        public readonly string $frozenQuantity,
        public readonly string $executedQuantity,
        public readonly string $minAmount,
        public readonly string $maxAmount,
        public readonly string $remark,
        public readonly int $status,
        public readonly string $createDate,
        public readonly array $payments,
        public readonly string $updateDate,
        public readonly string $feeRate,
        public readonly int $version,
        public readonly int $paymentPeriod,
        public readonly string $itemType,
    ) {}
}

