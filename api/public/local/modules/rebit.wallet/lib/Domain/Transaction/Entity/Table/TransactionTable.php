<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Transaction\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Wallet\Domain\Transaction\Entity\Transaction;
use Rebit\Wallet\Domain\Transaction\Entity\TransactionCollection;

/**
 * DataManager для HL-блока RebitTransaction.
 */
final class TransactionTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_transaction';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_USER_ID'),
            new IntegerField('UF_CURRENCY_ID'),
            new StringField('UF_TYPE'),
            new FloatField('UF_AMOUNT'),
            new FloatField('UF_BALANCE_AFTER'),
            new IntegerField('UF_TRADE_ID'),
            new StringField('UF_DESCRIPTION'),
            new StringField('UF_BYBIT_TX_ID'),
            new DatetimeField('UF_CREATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return Transaction::class;
    }

    public static function getCollectionClass(): string
    {
        return TransactionCollection::class;
    }
}
