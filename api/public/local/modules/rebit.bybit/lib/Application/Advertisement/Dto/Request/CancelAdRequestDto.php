<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Request;

use Rebit\Share\Shared\Interface\RequestDtoInterface;

final class CancelAdRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $itemId,
    ) {}
}

