<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Repository;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserFieldTable;
use Rebit\Share\Shared\Exception\RebitException;

abstract class AbstractHLBlockRepository extends AbstractRepository
{
    protected int $hlBlockId;

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws RebitException|SystemException
     */
    public function __construct(string $hlBlockName)
    {
        $hlBlockData = HighloadBlockTable::getList([
            'filter' => ['NAME' => $hlBlockName],
            'select' => ['ID', 'TABLE_NAME'],
            'cache' => ['ttl' => 86400],
        ])->fetch();

        if (!$hlBlockData) {
            throw new RebitException('HighloadBlock не найден: ' . $hlBlockName);
        }

        $this->hlBlockId = (int)$hlBlockData['ID'];

        $hlBlock = HighloadBlockTable::getById($this->hlBlockId)->fetch();
        $entity = HighloadBlockTable::compileEntity($hlBlock);
        $dataClass = $entity->getDataClass();

        parent::__construct($dataClass);
    }

    /**
     * Возвращает ID значения списка для UF\_* поля HL‑блока.
     * Ищет по VALUE (по умолчанию) или по XML\_ID.
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getEnumValueId(string $fieldName, string $value, bool $byXmlId = false): ?int
    {
        $fieldName = \strtoupper($fieldName);

        $userField = UserFieldTable::getList([
            'filter' => [
                '=ENTITY_ID' => 'HLBLOCK_' . $this->hlBlockId,
                '=FIELD_NAME' => $fieldName,
            ],
            'select' => ['ID'],
            'cache' => ['ttl' => 3600],
        ])->fetch();

        if (!$userField || !isset($userField['ID'])) {
            return null;
        }

        $filter = [
            'USER_FIELD_ID' => (int)$userField['ID'],
            ($byXmlId ? 'XML_ID' : 'VALUE') => $value,
        ];

        $enum = new \CUserFieldEnum();
        $res = $enum->GetList([], $filter);

        if ($row = $res->Fetch()) {
            return (int)$row['ID'];
        }

        return null;
    }

    /**
     * Утилита: получить ID по VALUE.
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getEnumValueIdByValue(string $fieldName, string $value): ?int
    {
        return $this->getEnumValueId($fieldName, $value);
    }

    /**
     * Утилита: получить ID по XML_ID.
     *
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getEnumValueIdByXmlId(string $fieldName, string $xmlId): ?int
    {
        return $this->getEnumValueId($fieldName, $xmlId, true);
    }
}
