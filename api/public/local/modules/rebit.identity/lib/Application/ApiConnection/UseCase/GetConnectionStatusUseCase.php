<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class GetConnectionStatusUseCase
{
    public function __construct(
        private ApiConnectionRepository $repository,
        private ApiKeyEncryptor $encryptor,
        private ApiKeyMasker $masker,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId): ApiConnectionResultDto
    {
        $connection = $this->repository->findByUserId($userId);

        if (null === $connection) {
            return new ApiConnectionResultDto(connected: false);
        }

        $status = ConnectionStatusEnum::from($connection->getUfStatus());

        if (ConnectionStatusEnum::Revoked === $status) {
            return new ApiConnectionResultDto(connected: false);
        }

        $apiKey = $this->encryptor->decrypt($connection->getUfApiKeyEncrypted());

        return new ApiConnectionResultDto(
            connected: true,
            status: $status,
            mode: ConnectionModeEnum::from($connection->getUfMode()),
            id: $connection->getId(),
            userId: $userId,
            maskedApiKey: $this->masker->mask($apiKey),
            createdAt: $connection->getUfCreatedAt()?->format('c'),
            verifiedAt: $connection->getUfLastVerifiedAt()?->format('c'),
        );
    }
}
