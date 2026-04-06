<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Entity\TradeCollection;

/**
 * DataManager для HL-блока RebitTrade.
 */
final class TradeTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_trade';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new StringField('UF_BYBIT_ORDER_ID'),
            new IntegerField('UF_BYBIT_STATUS'),
            new IntegerField('UF_BUYER_USER_ID'),
            new IntegerField('UF_SELLER_USER_ID'),
            new IntegerField('UF_ADVERTISEMENT_ID'),
            new IntegerField('UF_ORDER_BOOK_ENTRY_ID'),
            new IntegerField('UF_CURRENCY_PAIR_ID'),
            new StringField('UF_SIDE'),
            (new FloatField('UF_PRICE'))->configureScale(8),
            (new FloatField('UF_QUANTITY'))->configureScale(8),
            (new FloatField('UF_FIAT_AMOUNT'))->configureScale(8),
            (new FloatField('UF_FEE'))->configureScale(8),
            new IntegerField('UF_PAYMENT_METHOD_ID'),
            new TextField('UF_PAYMENT_DETAILS'),
            new TextField('UF_COMMENT'),
            new StringField('UF_STATUS'),
            new DatetimeField('UF_PAYMENT_DEADLINE'),
            new DatetimeField('UF_PAID_AT'),
            new DatetimeField('UF_CONFIRMED_AT'),
            new DatetimeField('UF_COMPLETED_AT'),
            new DatetimeField('UF_CANCELLED_AT'),
            new StringField('UF_CANCEL_REASON'),
            new StringField('UF_COUNTERPARTY_NAME'),
            new DatetimeField('UF_CREATED_AT'),
            new DatetimeField('UF_UPDATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return Trade::class;
    }

    public static function getCollectionClass(): string
    {
        return TradeCollection::class;
    }
}
