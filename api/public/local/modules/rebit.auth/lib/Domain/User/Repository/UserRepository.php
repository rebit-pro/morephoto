<?php

declare(strict_types=1);

namespace Rebit\Auth\Domain\User\Repository;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

final readonly class UserRepository
{
    /**
     * @return array{
     *     ID: int,
     *     UF_TOKEN_EXPIRES_AT: mixed,
     * }|null
     */
    public function findByToken(string $token): ?array
    {
        $row = UserTable::getList([
            'filter' => ['=UF_TOKEN' => $token],
            'select' => ['ID', 'UF_TOKEN_EXPIRES_AT'],
            'limit' => 1,
        ])->fetch();

        if (false === $row) {
            return null;
        }

        return $row;
    }

    /**
     * @return array{
     *     ID: int,
     *     PASSWORD: string,
     * }|null
     */
    public function findActiveByEmail(string $email): ?array
    {
        $row = UserTable::getList([
            'filter' => ['=EMAIL' => $email, '=ACTIVE' => 'Y'],
            'select' => ['ID', 'PASSWORD'],
            'limit' => 1,
        ])->fetch();

        if (false === $row) {
            return null;
        }

        return $row;
    }

    public function updateToken(int $userId, string $token, DateTime $expiresAt): void
    {
        $user = new \CUser();
        $user->Update($userId, [
            'UF_TOKEN' => $token,
            'UF_TOKEN_EXPIRES_AT' => $expiresAt->toString(),
        ]);
    }

    public function clearToken(int $userId): void
    {
        $user = new \CUser();
        $user->Update($userId, [
            'UF_TOKEN' => '',
            'UF_TOKEN_EXPIRES_AT' => false,
        ]);
    }
}
