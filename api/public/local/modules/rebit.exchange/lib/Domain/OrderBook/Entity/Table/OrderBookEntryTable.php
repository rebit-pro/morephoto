<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\OrderBook\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntryCollection;

/**
 * DataManager для HL-блока RebitOrderBook.
 */
final class OrderBookEntryTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_order_book';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new StringField('UF_BYBIT_ORDER_ID'),
            new IntegerField('UF_CURRENCY_PAIR_ID'),
            new StringField('UF_SIDE'),
            new FloatField('UF_PRICE'),
            new FloatField('UF_QUANTITY'),
            new FloatField('UF_MIN_AMOUNT'),
            new FloatField('UF_MAX_AMOUNT'),
            new StringField('UF_COUNTERPARTY_NAME'),
            new FloatField('UF_COUNTERPARTY_RATING'),
            new IntegerField('UF_COUNTERPARTY_TRADES'),
            new FloatField('UF_COUNTERPARTY_COMPLETION_RATE'),
            new StringField('UF_PAYMENT_METHOD_IDS'),
            new IntegerField('UF_PAYMENT_TIME_LIMIT'),
            new DatetimeField('UF_SYNCED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return OrderBookEntry::class;
    }

    public static function getCollectionClass(): string
    {
        return OrderBookEntryCollection::class;
    }
}
