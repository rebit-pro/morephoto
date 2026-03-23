<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\Service;

use Rebit\Identity\Domain\ApiConnection\Entity\Table\ApiConnectionTable;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionDto;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Bybit\BybitCredentials;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class BybitConnectionResolver implements BybitConnectionResolverInterface
{
    public function __construct(
        private ApiConnectionRepository $repository,
        private ApiKeyEncryptor $encryptor,
    ) {}

    /**
     * @throws HttpException
     */
    public function resolve(int $userId): BybitConnectionDto
    {
        $connection = $this->repository->findActiveByUserId($userId);

        if (null === $connection) {
            throw new HttpException('Нет активного подключения к Bybit', 400);
        }

        $credentials = new BybitCredentials(
            apiKey: $this->encryptor->decrypt($connection->getUfApiKeyEncrypted()),
            apiSecret: $this->encryptor->decrypt($connection->getUfSecretKeyEncrypted()),
        );

        $environment = match (ConnectionModeEnum::from($connection->getUfMode())) {
            ConnectionModeEnum::Testnet => BybitEnvironmentEnum::Testnet,
            ConnectionModeEnum::Mainnet => BybitEnvironmentEnum::Mainnet,
        };

        return new BybitConnectionDto($credentials, $environment);
    }

    /**
     * @return array<int, int>
     */
    public function getActiveUserIds(): array
    {
        /** @var array<int, array{UF_USER_ID: int|string}> $rows */
        $rows = ApiConnectionTable::query()
            ->setSelect(['UF_USER_ID'])
            ->where('UF_STATUS', ConnectionStatusEnum::Active->value)
            ->exec()
            ->fetchAll()
        ;

        return array_map(
            static fn(array $row): int => (int)$row['UF_USER_ID'],
            $rows,
        );
    }
}
