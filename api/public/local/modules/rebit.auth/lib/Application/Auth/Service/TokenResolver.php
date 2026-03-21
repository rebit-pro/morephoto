<?php

declare(strict_types=1);

namespace Rebit\Auth\Application\Auth\Service;

use Bitrix\Main\Type\DateTime;
use Rebit\Auth\Domain\User\Repository\UserRepository;
use Rebit\Share\Application\Contract\Auth\TokenResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class TokenResolver implements TokenResolverInterface
{
    public function __construct(
        private UserRepository $repository,
    ) {}

    /**
     * @throws HttpException
     */
    public function resolveUserId(string $token): int
    {
        $row = $this->repository->findByToken($token);

        if (null === $row) {
            throw new HttpException('Unauthorized', 401);
        }

        $expiresAt = $row['UF_TOKEN_EXPIRES_AT'];

        if ($expiresAt instanceof DateTime && $expiresAt->getTimestamp() < time()) {
            throw new HttpException('Token expired', 401);
        }

        return (int) $row['ID'];
    }
}
