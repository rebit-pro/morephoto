<?php

declare(strict_types=1);

namespace Rebit\Auth\Domain\User\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Auth\Domain\User\Entity\UserCredentials;

interface LoginUserRepositoryInterface
{
    public function findActiveByEmail(string $email): ?UserCredentials;

    public function updateToken(int $userId, string $token, DateTime $expiresAt): void;
}
