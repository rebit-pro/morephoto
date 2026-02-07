<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Contract;

interface RequestIdGeneratorInterface
{
    public function generate(): string;
}
