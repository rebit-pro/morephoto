<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Bybit;

final readonly class BybitTradePaymentTermDto
{
    public function __construct(
        public string $id,
        public string $realName,
        public int $paymentType,
        public string $bankName,
        public string $branchName,
        public string $accountNo,
        public string $qrcode,
    ) {}
}
