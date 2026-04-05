<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Advertisement\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;
use Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection;

/**
 * DataManager для HL-блока RebitAdvertisement.
 */
final class AdvertisementTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_advertisement';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_USER_ID'),
            new StringField('UF_BYBIT_AD_ID'),
            new IntegerField('UF_CURRENCY_PAIR_ID'),
            new StringField('UF_SIDE'),
            new StringField('UF_PRICE_TYPE'),
            (new FloatField('UF_PRICE'))->configureScale(8),
            (new FloatField('UF_PREMIUM'))->configureScale(8),
            (new FloatField('UF_QUANTITY'))->configureScale(8),
            (new FloatField('UF_QUANTITY_REMAINING'))->configureScale(8),
            (new FloatField('UF_MIN_AMOUNT'))->configureScale(8),
            (new FloatField('UF_MAX_AMOUNT'))->configureScale(8),
            new StringField('UF_PAYMENT_METHOD_IDS'),
            new IntegerField('UF_PAYMENT_PERIOD'),
            (new FloatField('UF_FEE_RATE'))->configureScale(8),
            new TextField('UF_CONDITIONS'),
            new IntegerField('UF_CHAT_SCRIPT_ID'),
            new StringField('UF_STATUS'),
            new DatetimeField('UF_CREATED_AT'),
            new DatetimeField('UF_UPDATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return Advertisement::class;
    }

    public static function getCollectionClass(): string
    {
        return AdvertisementCollection::class;
    }
}
