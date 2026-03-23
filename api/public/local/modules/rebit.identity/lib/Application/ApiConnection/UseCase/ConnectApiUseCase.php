<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Bybit\Application\Shared\Dto\BybitCredentialsDto;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Bybit\Infrastructure\Exception\BybitApiException;
use Rebit\Bybit\Shared\Enum\BybitEnvironmentEnum;
use Rebit\Identity\Application\ApiConnection\Dto\Request\ConnectApiRequestDto;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Rebit\Share\Shared\Exception\HttpException;

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
     * @throws \Exception
     */
    public function execute(ConnectApiRequestDto $dto, int $userId): ApiConnectionResultDto
    {
        $mode = ConnectionModeEnum::from($dto->mode);
        $environment = $this->resolveEnvironment($mode);
        $credentials = new BybitCredentialsDto($dto->apiKey, $dto->secretKey);

        // Отзываем предыдущее активное подключение (инвариант: только одно активное)
        $this->repository->revokeByUserId($userId);

        // Верификация ключей через тестовый запрос к Bybit
        $status = $this->verifyCredentials($credentials, $environment);

        $now = new DateTime();

        $connectionId = $this->repository->create([
            'UF_USER_ID' => $userId,
            'UF_API_KEY_ENCRYPTED' => $this->encryptor->encrypt($dto->apiKey),
            'UF_SECRET_KEY_ENCRYPTED' => $this->encryptor->encrypt($dto->secretKey),
            'UF_MODE' => $mode->value,
            'UF_STATUS' => $status->value,
            'UF_CREATED_AT' => $now,
            'UF_UPDATED_AT' => $now,
            'UF_VERIFIED_AT' => ConnectionStatusEnum::Active === $status ? $now : null,
        ]);

        return new ApiConnectionResultDto(
            connected: true,
            id: $connectionId,
            userId: $userId,
            status: $status,
            mode: $mode,
            maskedApiKey: $this->masker->mask($dto->apiKey),
            createdAt: $now->format('c'),
            verifiedAt: ConnectionStatusEnum::Active === $status ? $now->format('c') : null,
        );
    }

    private function verifyCredentials(
        BybitCredentialsDto $credentials,
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
