<?php

declare(strict_types=1);

namespace Rebit\Notification\Application\Notification\Port;

/**
 * Порт для получения email пользователя по ID.
 *
 * Используется handler'ом для обогащения payload перед отправкой в каналы.
 */
interface UserEmailResolverInterface
{
    public function resolve(int $userId): ?string;
}
