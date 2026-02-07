<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http\Generator;

use Ramsey\Uuid\Uuid;
use Rebit\Bybit\Infrastructure\Http\Contract\RequestIdGeneratorInterface;

final class UuidRequestIdGenerator implements RequestIdGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}