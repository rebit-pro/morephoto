<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection;
use Rebit\Identity\Domain\ApiConnection\Entity\Table\ApiConnectionTable;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Share\Shared\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class ApiConnectionRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findActiveByUserId(int $userId): ?ApiConnection
    {
        return $this->query(
            fn(): ?ApiConnection => ApiConnectionTable::query()
                ->setSelect(['*'])
                ->where('UF_USER_ID', $userId)
                ->where('UF_STATUS', ConnectionStatusEnum::Active->value)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?ApiConnection
    {
        return $this->query(
            fn(): ?ApiConnection => ApiConnectionTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByUserId(int $userId): ?ApiConnection
    {
        return $this->query(
            fn(): ?ApiConnection => ApiConnectionTable::query()
                ->setSelect(['*'])
                ->where('UF_USER_ID', $userId)
                ->setOrder(['ID' => 'DESC'])
                ->setLimit(1)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * Последнее не-отозванное подключение пользователя (Active, Invalid, PendingVerification).
     *
     * @throws RepositoryException
     */
    public function findNonRevokedByUserId(int $userId): ?ApiConnection
    {
        return $this->query(
            fn(): ?ApiConnection => ApiConnectionTable::query()
                ->setSelect(['*'])
                ->where('UF_USER_ID', $userId)
                ->whereNot('UF_STATUS', ConnectionStatusEnum::Revoked->value)
                ->setOrder(['ID' => 'DESC'])
                ->setLimit(1)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function save(ApiConnection $connection): void
    {
        $this->persist($connection);
    }

    /**
     * @throws RepositoryException
     */
    public function create(
        int $userId,
        string $apiKeyEncrypted,
        string $secretKeyEncrypted,
        string $mode,
        ConnectionStatusEnum $status,
        ?DateTime $lastVerifiedAt = null,
    ): ApiConnection {
        $now = new DateTime();

        /** @var ApiConnection $connection */
        $connection = ApiConnectionTable::createObject()
            ->setUfUserId($userId)
            ->setUfApiKeyEncrypted($apiKeyEncrypted)
            ->setUfSecretKeyEncrypted($secretKeyEncrypted)
            ->setUfMode($mode)
            ->setUfStatus($status->value)
            ->setUfLastVerifiedAt($lastVerifiedAt)
            ->setUfCreatedAt($now)
            ->setUfUpdatedAt($now)
        ;

        $this->persist($connection);

        return $connection;
    }

    /**
     * @throws RepositoryException
     */
    public function revokeByUserId(int $userId): void
    {
        $connection = $this->findNonRevokedByUserId($userId);

        if (null === $connection) {
            return;
        }

        $connection
            ->setUfStatus(ConnectionStatusEnum::Revoked->value)
            ->setUfUpdatedAt(new DateTime())
        ;

        $this->persist($connection);
    }

    /**
     * Список userId с активным подключением.
     *
     * @return array<int, int>
     *
     * @throws RepositoryException
     */
    public function getActiveUserIds(): array
    {
        return $this->query(function(): array {
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
        });
    }
}
