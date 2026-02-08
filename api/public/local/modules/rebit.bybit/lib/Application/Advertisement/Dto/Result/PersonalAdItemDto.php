<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Result;

final readonly class PersonalAdItemDto
{
    /**
     * @param string[] $payments
     */
    public function __construct(
        public string $id,
        public string $accountId,
        public string $userId,
        public string $nickName,
        public string $tokenId,
        public string $currencyId,
        /** 0: buy; 1: sell */
        public int $side,
        /** 0: fixed rate; 1: floating rate */
        public int $priceType,
        public string $price,
        public string $premium,
        public string $lastQuantity,
        public string $quantity,
        public string $frozenQuantity,
        public string $executedQuantity,
        public string $minAmount,
        public string $maxAmount,
        public string $remark,
        /** 10: online; 20: offline; 30: completed */
        public int $status,
        public string $createDate,
        public array $payments,
        public int $paymentPeriod,
        public string $itemType,
        public string $updateDate,
    ) {}
}
