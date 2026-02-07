<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Contract;

interface ClockInterface
{
    public function nowMs(): int;
}
