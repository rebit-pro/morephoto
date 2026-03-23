<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Identity\Application\ApiConnection\Dto\Request\ConnectApiRequestDto;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitCredentials;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class ConnectApiUseCase
{
    private const string VERIFY_ENDPOINT = '/v5/user/query-api';

    public function __construct(
        private ApiConnectionRepository $repository,
        private ApiKeyEncryptor $encryptor,
        private ApiKeyMasker $masker,
        private BybitClientInterface $bybitClient,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(ConnectApiRequestDto $dto, int $userId): ApiConnectionResultDto
    {
        $mode = ConnectionModeEnum::from($dto->mode);
        $environment = $this->resolveEnvironment($mode);
        $credentials = new BybitCredentials($dto->apiKey, $dto->secretKey);

        // Отзываем предыдущее активное подключение (инвариант: только одно активное)
        $this->repository->revokeByUserId($userId);

        // Верификация ключей через тестовый запрос к Bybit
        $status = $this->verifyCredentials($credentials, $environment);

        $connection = $this->repository->create(
            userId: $userId,
            apiKeyEncrypted: $this->encryptor->encrypt($dto->apiKey),
            secretKeyEncrypted: $this->encryptor->encrypt($dto->secretKey),
            mode: $mode->value,
            status: $status,
            lastVerifiedAt: ConnectionStatusEnum::Active === $status ? new DateTime() : null,
        );

        return new ApiConnectionResultDto(
            connected: true,
            status: $status,
            mode: $mode,
            id: $connection->getId(),
            userId: $userId,
            maskedApiKey: $this->masker->mask($dto->apiKey),
            createdAt: $connection->getUfCreatedAt()?->format('c'),
            verifiedAt: $connection->getUfLastVerifiedAt()?->format('c'),
        );
    }

    private function verifyCredentials(
        BybitCredentials $credentials,
        BybitEnvironmentEnum $environment,
    ): ConnectionStatusEnum {
        try {
            $this->bybitClient->get(self::VERIFY_ENDPOINT, $credentials, $environment);

            return ConnectionStatusEnum::Active;
        } catch (BybitApiException) {
            return ConnectionStatusEnum::Invalid;
        }
    }

    private function resolveEnvironment(ConnectionModeEnum $mode): BybitEnvironmentEnum
    {
        return match ($mode) {
            ConnectionModeEnum::Testnet => BybitEnvironmentEnum::Testnet,
            ConnectionModeEnum::Mainnet => BybitEnvironmentEnum::Mainnet,
        };
    }
}
