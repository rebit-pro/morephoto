<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;

final readonly class GetConnectionStatusUseCase
{
    public function __construct(
        private ApiConnectionRepository $repository,
        private ApiKeyEncryptor $encryptor,
        private ApiKeyMasker $masker,
    ) {}

    public function execute(int $userId): ApiConnectionResultDto
    {
        $connection = $this->repository->findByUserId($userId);

        if (false === $connection) {
            return new ApiConnectionResultDto(connected: false);
        }

        $apiKey = $this->encryptor->decrypt($connection['UF_API_KEY_ENCRYPTED']);

        return new ApiConnectionResultDto(
            connected: true,
            status: ConnectionStatusEnum::from($connection['UF_STATUS']),
            mode: ConnectionModeEnum::from($connection['UF_MODE']),
            id: (int)$connection['ID'],
            userId: $userId,
            maskedApiKey: $this->masker->mask($apiKey),
            createdAt: $this->formatDateTime($connection['UF_CREATED_AT']),
            verifiedAt: null !== $connection['UF_VERIFIED_AT']
                ? $this->formatDateTime($connection['UF_VERIFIED_AT'])
                : null,
        );
    }

    private function formatDateTime(mixed $value): string
    {
        return $value instanceof DateTime
            ? $value->format('c')
            : (string)$value;
    }
}
