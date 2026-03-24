<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class TransactionListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, TransactionResultDto> $transactions
     */
    public function __construct(
        public array $transactions,
        public int $total,
    ) {}
}
