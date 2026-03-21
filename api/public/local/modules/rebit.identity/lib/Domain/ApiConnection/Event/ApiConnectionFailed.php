<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Event;

final readonly class ApiConnectionFailed
{
    public function __construct(
        public int $userId,
        public string $reason,
    ) {}
}
