<?php

declare(strict_types=1);

namespace Rebit\Auth\Application\Auth\Service;

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
        $userToken = $this->repository->findByToken($token);

        if (null === $userToken) {
            throw new HttpException('Unauthorized', 401);
        }

        if (null !== $userToken->expiresAt && $userToken->expiresAt->getTimestamp() < time()) {
            throw new HttpException('Token expired', 401);
        }

        return $userToken->userId;
    }
}
