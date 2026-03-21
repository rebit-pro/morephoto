<?php

declare(strict_types=1);

namespace Rebit\Auth\Domain\User\Service;

final readonly class TokenGenerator
{
    /**
     * Генерирует криптографически стойкий токен.
     *
     * @throws \Random\RandomException
     */
    public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}
