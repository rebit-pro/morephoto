<?php

declare(strict_types=1);

namespace Rebit\Auth\Domain\User\Repository;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Rebit\Auth\Domain\User\Entity\UserCredentials;
use Rebit\Auth\Domain\User\Entity\UserToken;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class UserRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findByToken(string $token): ?UserToken
    {
        return $this->query(function () use ($token): ?UserToken {
            $row = UserTable::query()
                ->setSelect(['ID', 'UF_TOKEN_EXPIRES_AT'])
                ->where('UF_TOKEN', $token)
                ->setLimit(1)
                ->exec()
                ->fetch()
            ;

            if (false === $row) {
                return null;
            }

            return new UserToken(
                userId: (int)$row['ID'],
                expiresAt: $row['UF_TOKEN_EXPIRES_AT'] instanceof DateTime
                    ? $row['UF_TOKEN_EXPIRES_AT']
                    : null,
            );
        });
    }

    /**
     * @throws RepositoryException
     */
    public function findActiveByEmail(string $email): ?UserCredentials
    {
        return $this->query(function () use ($email): ?UserCredentials {
            $row = UserTable::query()
                ->setSelect(['ID', 'PASSWORD', 'EMAIL', 'NAME'])
                ->enablePrivateFields()
                ->where('EMAIL', $email)
                ->where('ACTIVE', 'Y')
                ->setLimit(1)
                ->exec()
                ->fetch()
            ;

            if (false === $row) {
                return null;
            }

            return new UserCredentials(
                id: (int)$row['ID'],
                passwordHash: (string)$row['PASSWORD'],
                email: (string)$row['EMAIL'],
                name: (string)$row['NAME'],
            );
        });
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
