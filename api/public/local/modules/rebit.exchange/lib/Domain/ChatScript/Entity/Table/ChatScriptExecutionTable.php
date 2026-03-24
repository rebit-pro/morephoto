<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecution;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecutionCollection;

/**
 * DataManager для очереди отложенного исполнения чат-скриптов.
 */
final class ChatScriptExecutionTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_chat_script_execution';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_TRADE_ID'),
            new IntegerField('UF_SCRIPT_ID'),
            new IntegerField('UF_USER_ID'),
            new IntegerField('UF_LAST_STEP_SORT'),
            new StringField('UF_STATUS'),
            new DatetimeField('UF_NEXT_RUN_AT'),
            new DatetimeField('UF_CREATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return ChatScriptExecution::class;
    }

    public static function getCollectionClass(): string
    {
        return ChatScriptExecutionCollection::class;
    }
}
