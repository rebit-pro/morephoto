<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Event;

final readonly class ApiConnectionRevoked
{
    public function __construct(
        public int $userId,
        public int $connectionId,
    ) {}
}
