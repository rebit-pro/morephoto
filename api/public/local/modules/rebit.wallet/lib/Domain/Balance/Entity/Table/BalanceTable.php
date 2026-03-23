<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Rebit\Wallet\Domain\Balance\Entity\Balance;
use Rebit\Wallet\Domain\Balance\Entity\BalanceCollection;

/**
 * DataManager для HL-блока RebitBalance.
 */
final class BalanceTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_balance';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_USER_ID'),
            new IntegerField('UF_CURRENCY_ID'),
            new FloatField('UF_AVAILABLE'),
            new FloatField('UF_LOCKED'),
            new FloatField('UF_TOTAL'),
            new DatetimeField('UF_SYNCED_AT'),
            new DatetimeField('UF_UPDATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return Balance::class;
    }

    public static function getCollectionClass(): string
    {
        return BalanceCollection::class;
    }
}
