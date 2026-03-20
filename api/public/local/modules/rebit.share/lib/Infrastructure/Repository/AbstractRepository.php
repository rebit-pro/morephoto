<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Repository;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\DB\Connection;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\SystemException;

/**
 * @template T of DataManager
 */
abstract class AbstractRepository
{
    /**
     * @var T
     */
    protected DataManager $dataManager;

    /**
     * @param class-string<T>|DataManager<T> $className
     *
     * @throws ArgumentException
     */
    public function __construct(DataManager|string $className)
    {
        if (is_string($className) && !class_exists($className)) {
            throw new ArgumentException(sprintf('Class %s is not exists', $className));
        }

        $this->dataManager = $className instanceof DataManager ? $className : new $className();
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getQuery(): Query
    {
        return $this->dataManager::query();
    }

    /**
     * @return T
     */
    public function getDataManager(): DataManager
    {
        return $this->dataManager;
    }

    /**
     * @throws ArgumentException
     * @throws SystemException
     */
    public function getEntity(): Entity
    {
        return $this->dataManager::getEntity();
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getTableName(): string
    {
        return $this->getEntity()->getDBTableName();
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getConnection(): Connection
    {
        return $this->getEntity()->getConnection();
    }
}
