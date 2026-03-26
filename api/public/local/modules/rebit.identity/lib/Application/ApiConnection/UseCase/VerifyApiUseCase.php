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
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitCredentials;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

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
     * @throws RepositoryException
     */
    public function execute(int $userId): ApiConnectionResultDto
    {
        $connection = $this->repository->findNonRevokedByUserId($userId);

        if (null === $connection) {
            throw new HttpException('API connection not found', 404);
        }

        $mode = ConnectionModeEnum::from($connection->getUfMode());
        $environment = match ($mode) {
            ConnectionModeEnum::Testnet => BybitEnvironmentEnum::Testnet,
            ConnectionModeEnum::Mainnet => BybitEnvironmentEnum::Mainnet,
        };

        $apiKey = $this->encryptor->decrypt($connection->getUfApiKeyEncrypted());

        $credentials = new BybitCredentials(
            apiKey: $apiKey,
            apiSecret: $this->encryptor->decrypt($connection->getUfSecretKeyEncrypted()),
        );

        try {
            $this->bybitClient->get(self::VERIFY_ENDPOINT, $credentials, $environment);
            $newStatus = ConnectionStatusEnum::Active;
        } catch (BybitApiException) {
            $newStatus = ConnectionStatusEnum::Invalid;
        }

        $now = new DateTime();

        $connection
            ->setUfStatus($newStatus->value)
            ->setUfLastVerifiedAt($now)
            ->setUfUpdatedAt($now)
        ;

        $this->repository->save($connection);

        return new ApiConnectionResultDto(
            connected: true,
            status: $newStatus,
            mode: $mode,
            id: $connection->getId(),
            userId: $userId,
            maskedApiKey: $this->masker->mask($apiKey),
            createdAt: $connection->getUfCreatedAt()?->format('c'),
            verifiedAt: $now->format('c'),
        );
    }
}
