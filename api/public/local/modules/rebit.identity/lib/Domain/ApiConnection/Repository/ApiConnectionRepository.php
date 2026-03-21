<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Repository;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Share\Infrastructure\Repository\AbstractHLBlockRepository;

final class ApiConnectionRepository extends AbstractHLBlockRepository
{
    private const string HL_BLOCK_NAME = 'RebitApiConnection';
    private const int TTL = 60;

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function __construct()
    {
        parent::__construct(self::HL_BLOCK_NAME);
    }

    /**
     * @return array<string, mixed>|false
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function findActiveByUserId(int $userId): array|false
    {
        return $this->getQuery()
            ->setSelect(['*'])
            ->where('UF_USER_ID', $userId)
            ->where('UF_STATUS', ConnectionStatusEnum::Active->value)
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetch();
    }

    /**
     * @return array<string, mixed>|false
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function findById(int $id): array|false
    {
        return $this->getQuery()
            ->setSelect(['*'])
            ->where('ID', $id)
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetch();
    }

    /**
     * @return array<string, mixed>|false
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function findByUserId(int $userId): array|false
    {
        return $this->getQuery()
            ->setSelect(['*'])
            ->where('UF_USER_ID', $userId)
            ->setOrder(['ID' => 'DESC'])
            ->setLimit(1)
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetch();
    }

    /**
     * @param array<string, mixed> $fields
     *
     * @throws \Exception
     */
    public function create(array $fields): int
    {
        $result = $this->getDataManager()::add($fields);

        if (!$result->isSuccess()) {
            throw new \RuntimeException(
                implode('; ', $result->getErrorMessages()),
            );
        }

        return $result->getId();
    }

    /**
     * @param array<string, mixed> $fields
     *
     * @throws \Exception
     */
    public function update(int $id, array $fields): void
    {
        $result = $this->getDataManager()::update($id, $fields);

        if (!$result->isSuccess()) {
            throw new \RuntimeException(
                implode('; ', $result->getErrorMessages()),
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function revokeByUserId(int $userId): void
    {
        $connection = $this->findActiveByUserId($userId);

        if (false === $connection) {
            return;
        }

        $this->update((int) $connection['ID'], [
            'UF_STATUS' => ConnectionStatusEnum::Revoked->value,
            'UF_REVOKED_AT' => new DateTime(),
        ]);
    }
}
