<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Transaction\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;

final readonly class TransactionResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $currencyId,
        public TransactionTypeEnum $type,
        public float $amount,
        public float $balanceAfter,
        public ?int $tradeId,
        public ?string $description,
        public ?string $bybitTxId,
        public string $createdAt,
    ) {}
}
