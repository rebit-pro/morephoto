<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Currency\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Exchange\Domain\Currency\Entity\Currency;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection;

/**
 * DataManager для HL-блока RebitCurrency.
 */
final class CurrencyTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_currency';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new StringField('UF_CODE'),
            new StringField('UF_NAME'),
            new StringField('UF_TYPE'),
            new IntegerField('UF_DECIMALS'),
            new IntegerField('UF_ICON'),
            (new BooleanField('UF_IS_ACTIVE'))
                ->configureValues(0, 1),
            new IntegerField('UF_SORT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return Currency::class;
    }

    public static function getCollectionClass(): string
    {
        return CurrencyCollection::class;
    }
}
