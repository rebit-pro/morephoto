<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class DisconnectApiUseCase
{
    public function __construct(
        private ApiConnectionRepository $repository,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $userId): void
    {
        $connection = $this->repository->findNonRevokedByUserId($userId);

        if (null === $connection) {
            throw new HttpException('API connection not found', 404);
        }

        $this->repository->revokeByUserId($userId);
    }
}
