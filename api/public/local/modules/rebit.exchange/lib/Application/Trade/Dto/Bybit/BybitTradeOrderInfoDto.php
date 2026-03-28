<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Bybit;

final readonly class BybitTradeOrderInfoDto
{
    /**
     * @param list<BybitTradePaymentTermDto> $paymentTermList
     */
    public function __construct(
        public string $id,
        public int $side,
        public string $itemId,
        public string $userId,
        public string $nickName,
        public string $makerUserId,
        public string $targetUserId,
        public string $targetNickName,
        public string $tokenId,
        public string $currencyId,
        public string $price,
        public string $quantity,
        public string $amount,
        public int $paymentType,
        public string $transferDate,
        public int $status,
        public string $createDate,
        public array $paymentTermList,
        public string $remark,
        public string $transferLastSeconds,
    ) {}
}
