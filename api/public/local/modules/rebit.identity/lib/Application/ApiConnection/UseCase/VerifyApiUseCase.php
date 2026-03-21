<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Bybit\Application\Shared\Dto\BybitCredentialsDto;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Bybit\Infrastructure\Exception\BybitApiException;
use Rebit\Bybit\Shared\Enum\BybitEnvironmentEnum;
use Rebit\Identity\Domain\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class VerifyApiUseCase
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

        $credentials = new BybitCredentialsDto(
            apiKey: $this->encryptor->decrypt($connection['UF_API_KEY_ENCRYPTED']),
            apiSecret: $this->encryptor->decrypt($connection['UF_API_SECRET_ENCRYPTED']),
        );

        try {
            $this->bybitClient->get(self::VERIFY_ENDPOINT, $credentials, $environment);
            $newStatus = ConnectionStatusEnum::Active;
        } catch (BybitApiException) {
            $newStatus = ConnectionStatusEnum::Invalid;
        }

        $connectionId = (int) $connection['ID'];

        $this->repository->update($connectionId, [
            'UF_STATUS' => $newStatus->value,
            'UF_VERIFIED_AT' => new DateTime(),
        ]);

        return new ApiConnectionResultDto(
            id: $connectionId,
            userId: $userId,
            status: $newStatus,
            mode: $mode,
            maskedApiKey: $this->maskApiKey(
                $this->encryptor->decrypt($connection['UF_API_KEY_ENCRYPTED']),
            ),
            createdAt: $connection['UF_CREATED_AT'] instanceof DateTime
                ? $connection['UF_CREATED_AT']->format('c')
                : (string) $connection['UF_CREATED_AT'],
            verifiedAt: (new DateTime())->format('c'),
        );
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
