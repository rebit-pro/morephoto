<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Bybit;

final readonly class BybitTradeOrderSummaryDto
{
    public function __construct(
        public string $id,
        public int $side,
        public string $amount,
        public string $price,
        public string $fee,
        public string $targetNickName,
        public string $targetUserId,
        public int $status,
        public string $createDate,
        public string $transferLastSeconds,
    ) {}
}
