<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Clock;

use Rebit\Bybit\Infrastructure\Http\Contract\ClockInterface;

final class SystemClock implements ClockInterface
{
    public function nowMs(): int
    {
        return (int)(microtime(true) * 1000);
    }
}
