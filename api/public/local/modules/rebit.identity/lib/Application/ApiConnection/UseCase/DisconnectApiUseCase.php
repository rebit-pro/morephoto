<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class DisconnectApiUseCase
{
    public function __construct(
        private ApiConnectionRepository $repository,
    ) {}

    /**
     * @throws HttpException
     * @throws \Exception
     */
    public function execute(int $userId): void
    {
        $connection = $this->repository->findActiveByUserId($userId);

        if (false === $connection) {
            throw new HttpException('Active API connection not found', 404);
        }

        $this->repository->revokeByUserId($userId);
    }
}
