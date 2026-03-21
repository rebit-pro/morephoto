<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Identity\Domain\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class GetConnectionStatusUseCase
{
    public function __construct(
        private ApiConnectionRepository $repository,
        private ApiKeyEncryptor $encryptor,
    ) {}

    /**
     * @throws HttpException
     */
    public function execute(int $userId): ApiConnectionResultDto
    {
        $connection = $this->repository->findByUserId($userId);

        if (false === $connection) {
            throw new HttpException('API connection not found', 404);
        }

        $apiKey = $this->encryptor->decrypt($connection['UF_API_KEY_ENCRYPTED']);
        $visibleChars = 4;

        $maskedApiKey = mb_strlen($apiKey) <= $visibleChars * 2
            ? str_repeat('*', mb_strlen($apiKey))
            : mb_substr($apiKey, 0, $visibleChars)
                . str_repeat('*', mb_strlen($apiKey) - $visibleChars * 2)
                . mb_substr($apiKey, -$visibleChars);

        return new ApiConnectionResultDto(
            id: (int) $connection['ID'],
            userId: $userId,
            status: ConnectionStatusEnum::from($connection['UF_STATUS']),
            mode: ConnectionModeEnum::from($connection['UF_MODE']),
            maskedApiKey: $maskedApiKey,
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
            : (string) $value;
    }
}
