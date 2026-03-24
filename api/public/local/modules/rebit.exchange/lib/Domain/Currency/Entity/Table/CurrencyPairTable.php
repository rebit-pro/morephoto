<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Currency\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection;

/**
 * DataManager для HL-блока RebitCurrencyPair.
 */
final class CurrencyPairTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_currency_pair';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_TOKEN_CURRENCY_ID'),
            new IntegerField('UF_FIAT_CURRENCY_ID'),
            new StringField('UF_CODE'),
            (new BooleanField('UF_IS_ACTIVE'))
                ->configureValues(0, 1),
            (new BooleanField('UF_IS_DEFAULT'))
                ->configureValues(0, 1),
            new IntegerField('UF_SORT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return CurrencyPair::class;
    }

    public static function getCollectionClass(): string
    {
        return CurrencyPairCollection::class;
    }
}
