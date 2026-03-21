<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Bybit\Application\Shared\Dto\BybitCredentialsDto;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Bybit\Infrastructure\Exception\BybitApiException;
use Rebit\Bybit\Shared\Enum\BybitEnvironmentEnum;
use Rebit\Identity\Domain\ApiConnection\Dto\Request\ConnectApiRequestDto;
use Rebit\Identity\Domain\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class ConnectApiUseCase
{
    private const string VERIFY_ENDPOINT = '/v5/user/query-api';

    public function __construct(
        private ApiConnectionRepository $repository,
        private ApiKeyEncryptor $encryptor,
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
        $credentials = new BybitCredentialsDto($dto->apiKey, $dto->apiSecret);

        // Отзываем предыдущее активное подключение (инвариант: только одно активное)
        $this->repository->revokeByUserId($userId);

        // Верификация ключей через тестовый запрос к Bybit
        $status = $this->verifyCredentials($credentials, $environment);

        $connectionId = $this->repository->create([
            'UF_USER_ID' => $userId,
            'UF_API_KEY_ENCRYPTED' => $this->encryptor->encrypt($dto->apiKey),
            'UF_API_SECRET_ENCRYPTED' => $this->encryptor->encrypt($dto->apiSecret),
            'UF_MODE' => $mode->value,
            'UF_STATUS' => $status->value,
            'UF_CREATED_AT' => new DateTime(),
            'UF_VERIFIED_AT' => ConnectionStatusEnum::Active === $status ? new DateTime() : null,
        ]);

        $maskedApiKey = $this->maskApiKey($dto->apiKey);

        return new ApiConnectionResultDto(
            id: $connectionId,
            userId: $userId,
            status: $status,
            mode: $mode,
            maskedApiKey: $maskedApiKey,
            createdAt: (new DateTime())->format('c'),
            verifiedAt: ConnectionStatusEnum::Active === $status ? (new DateTime())->format('c') : null,
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

    private function maskApiKey(string $apiKey): string
    {
        $visibleChars = 4;

        if (mb_strlen($apiKey) <= $visibleChars * 2) {
            return str_repeat('*', mb_strlen($apiKey));
        }

        return mb_substr($apiKey, 0, $visibleChars)
            . str_repeat('*', mb_strlen($apiKey) - $visibleChars * 2)
            . mb_substr($apiKey, -$visibleChars);
    }
}
