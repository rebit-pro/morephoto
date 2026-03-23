<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Service;

final readonly class ApiKeyMasker
{
    private const int VISIBLE_CHARS = 4;

    public function mask(string $apiKey): string
    {
        $length = mb_strlen($apiKey);

        if ($length <= self::VISIBLE_CHARS * 2) {
            return str_repeat('*', $length);
        }

        return mb_substr($apiKey, 0, self::VISIBLE_CHARS)
            . str_repeat('*', $length - self::VISIBLE_CHARS * 2)
            . mb_substr($apiKey, -self::VISIBLE_CHARS);
    }
}
