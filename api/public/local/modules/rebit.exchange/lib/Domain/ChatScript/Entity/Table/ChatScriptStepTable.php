<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;

/**
 * DataManager для HL-блока RebitTradeChatScriptStep.
 */
final class ChatScriptStepTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_trade_chat_script_step';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_SCRIPT_ID'),
            new IntegerField('UF_SORT'),
            new TextField('UF_MESSAGE'),
            new IntegerField('UF_DELAY_SECONDS'),
        ];
    }

    public static function getObjectClass(): string
    {
        return ChatScriptStep::class;
    }

    public static function getCollectionClass(): string
    {
        return ChatScriptStepCollection::class;
    }
}
