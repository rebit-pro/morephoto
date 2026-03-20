<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Repository;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;

abstract class AbstractHLBlockRepository extends AbstractRepository
{
    private const int HL_BLOCK_ID_CACHE_TIME = 86400;

    protected int $hlBlockId;

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function __construct(string $hlBlockName)
    {
        $hlBlockData = HighloadBlockTable::getList([
            'filter' => ['NAME' => $hlBlockName],
            'select' => ['ID', 'TABLE_NAME'],
            'cache' => ['ttl' => self::HL_BLOCK_ID_CACHE_TIME],
        ])->fetch();

        if (false === $hlBlockData) {
            throw new ArgumentException('HighloadBlock не найден: ' . $hlBlockName);
        }

        $this->hlBlockId = (int)$hlBlockData['ID'];

        parent::__construct($this->resolveDataClass());
    }

    public function getHLBlockId(): int
    {
        return $this->hlBlockId;
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    private function resolveDataClass(): DataManager|string
    {
        $hlBlock = HighloadBlockTable::getById($this->hlBlockId)->fetch();

        return HighloadBlockTable::compileEntity($hlBlock)->getDataClass();
    }
}
