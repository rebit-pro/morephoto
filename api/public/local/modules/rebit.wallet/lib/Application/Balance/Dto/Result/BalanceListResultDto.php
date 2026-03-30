<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class BalanceListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, BalanceResultDto> $balances
     */
    public function __construct(
        public array $balances,
        public ?float $totalRubEquivalent = null,
    ) {}
}
