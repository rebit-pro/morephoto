<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Bybit\Application\Shared\Dto\BybitCredentialsDto;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Bybit\Infrastructure\Exception\BybitApiException;
use Rebit\Bybit\Shared\Enum\BybitEnvironmentEnum;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class VerifyApiUseCase
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
    public function execute(int $userId): ApiConnectionResultDto
    {
        $connection = $this->repository->findActiveByUserId($userId);

        if (false === $connection) {
            throw new HttpException('Active API connection not found', 404);
        }

        $mode = ConnectionModeEnum::from($connection['UF_MODE']);
        $environment = match ($mode) {
            ConnectionModeEnum::Testnet => BybitEnvironmentEnum::Testnet,
            ConnectionModeEnum::Mainnet => BybitEnvironmentEnum::Mainnet,
        };

        $apiKey = $this->encryptor->decrypt($connection['UF_API_KEY_ENCRYPTED']);

        $credentials = new BybitCredentialsDto(
            apiKey: $apiKey,
            apiSecret: $this->encryptor->decrypt($connection['UF_SECRET_KEY_ENCRYPTED']),
        );

        try {
            $this->bybitClient->get(self::VERIFY_ENDPOINT, $credentials, $environment);
            $newStatus = ConnectionStatusEnum::Active;
        } catch (BybitApiException) {
            $newStatus = ConnectionStatusEnum::Invalid;
        }

        $connectionId = (int)$connection['ID'];
        $now = new DateTime();

        $this->repository->update($connectionId, [
            'UF_STATUS' => $newStatus->value,
            'UF_VERIFIED_AT' => $now,
            'UF_UPDATED_AT' => $now,
        ]);

        return new ApiConnectionResultDto(
            connected: true,
            id: $connectionId,
            userId: $userId,
            status: $newStatus,
            mode: $mode,
            maskedApiKey: $this->masker->mask($apiKey),
            createdAt: $connection['UF_CREATED_AT'] instanceof DateTime
                ? $connection['UF_CREATED_AT']->format('c')
                : (string)$connection['UF_CREATED_AT'],
            verifiedAt: $now->format('c'),
        );
    }
}
