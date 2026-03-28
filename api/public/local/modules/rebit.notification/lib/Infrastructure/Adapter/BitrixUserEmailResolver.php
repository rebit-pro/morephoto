<?php

declare(strict_types=1);

namespace Rebit\Notification\Infrastructure\Adapter;

use Rebit\Notification\Application\Notification\Port\UserEmailResolverInterface;

/**
 * Получение email пользователя через Bitrix CUser API.
 */
final readonly class BitrixUserEmailResolver implements UserEmailResolverInterface
{
    public function resolve(int $userId): ?string
    {
        if (0 >= $userId) {
            return null;
        }

        $dbUser = \CUser::GetByID($userId)->Fetch();

        if (false === $dbUser) {
            return null;
        }

        $email = (string)($dbUser['EMAIL'] ?? '');

        return '' !== $email ? $email : null;
    }
}
