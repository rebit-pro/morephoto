<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\PaymentMethod\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod;
use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection;

/**
 * DataManager для HL-блока RebitPaymentMethod.
 */
final class PaymentMethodTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_payment_method';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_BYBIT_ID'),
            new StringField('UF_CODE'),
            new StringField('UF_NAME'),
            new IntegerField('UF_ICON'),
            (new BooleanField('UF_IS_ACTIVE'))
                ->configureValues(0, 1),
            new IntegerField('UF_SORT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return PaymentMethod::class;
    }

    public static function getCollectionClass(): string
    {
        return PaymentMethodCollection::class;
    }
}
