<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\TradeChat\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage;
use Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection;

/**
 * DataManager для HL-блока RebitTradeMessage.
 */
final class TradeMessageTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_trade_message';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_TRADE_ID'),
            new IntegerField('UF_USER_ID'),
            new TextField('UF_MESSAGE'),
            new StringField('UF_MESSAGE_TYPE'),
            new StringField('UF_CONTENT_TYPE'),
            new StringField('UF_BYBIT_MSG_UUID'),
            new StringField('UF_FILE_NAME'),
            new IntegerField('UF_SCRIPT_STEP_ID'),
            (new BooleanField('UF_IS_READ'))
                ->configureValues(0, 1),
            new DatetimeField('UF_CREATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return TradeMessage::class;
    }

    public static function getCollectionClass(): string
    {
        return TradeMessageCollection::class;
    }
}
