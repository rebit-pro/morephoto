<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Dto\Bybit;

final readonly class BybitBalanceListDto
{
    /**
     * @param list<BybitBalanceDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
