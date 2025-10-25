<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Helpers;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Model\UserFieldEnumTable;

class BitrixEnumHelper
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function getEnumValue(int $id): string
    {
        return UserFieldEnumTable::query()
            ->setFilter([
                '=ID' => $id,
            ])
            ->setSelect(['VALUE'])
            ->fetch()['VALUE']
        ;
    }

    /**
     * Получить ID значения из списка пользовательского поля по его значению
     *
     * @param string $tableName Название таблицы (например, 'AnswerTable')
     * @param string $fieldName Название поля (например, 'UF_TYPE')
     * @param string $value     Значение (например, 'STATIC')
     *
     * @throws \Exception
     */
    public static function getEnumValueId(string $tableName, string $fieldName, string $value): int
    {
        // Получаем ID Highload-блока по имени таблицы
        $hlblock = HighloadBlockTable::query()
            ->setFilter(['=TABLE_NAME' => $tableName])
            ->setSelect(['ID'])
            ->fetch()
        ;

        if (!$hlblock) {
            throw new \Exception("Highload block with table '{$tableName}' not found");
        }

        $entityId = 'HLBLOCK_' . $hlblock['ID'];

        // Получаем пользовательское поле
        $userField = \CUserTypeEntity::GetList(
            [],
            [
                'ENTITY_ID' => $entityId,
                'FIELD_NAME' => $fieldName,
            ],
        )->Fetch();

        if (!$userField) {
            throw new \Exception("User field {$fieldName} not found for entity {$entityId}");
        }

        // Получаем значение по VALUE
        $enumValue = UserFieldEnumTable::query()
            ->setFilter([
                '=USER_FIELD_ID' => $userField['ID'],
                '=VALUE' => $value,
            ])
            ->setSelect(['ID'])
            ->fetch()
        ;

        if (!$enumValue) {
            throw new \Exception("Enum value '{$value}' not found for field {$fieldName}");
        }

        return (int)$enumValue['ID'];
    }
}
