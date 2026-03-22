<?php

declare(strict_types=1);

namespace Rebit\Auth\Domain\User\Service;

use Random\RandomException;

final readonly class TokenGenerator
{
    /**
     * Генерирует криптографически стойкий токен.
     *
     * @throws RandomException
     */
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}
