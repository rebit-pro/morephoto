<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection;

/**
 * DataManager для HL-блока RebitTradeChatScript.
 */
final class ChatScriptTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_trade_chat_script';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_USER_ID'),
            new StringField('UF_NAME'),
            (new BooleanField('UF_IS_ACTIVE'))
                ->configureValues(0, 1),
            new DatetimeField('UF_CREATED_AT'),
            new DatetimeField('UF_UPDATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return ChatScript::class;
    }

    public static function getCollectionClass(): string
    {
        return ChatScriptCollection::class;
    }
}
