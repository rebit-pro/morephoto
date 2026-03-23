<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class BalanceListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, BalanceResultDto> $balances
     */
    public function __construct(
        public array $balances,
    ) {}
}
