<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Request;

use Rebit\Share\Shared\Interface\RequestDtoInterface;

final class OrderBookRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $tokenId,
        public string $currencyId,
        /** @var string $side "0": buy; "1": sell */
        public string $side,
        public string $page = '1',
        public string $size = '10',
    ) {}
}

