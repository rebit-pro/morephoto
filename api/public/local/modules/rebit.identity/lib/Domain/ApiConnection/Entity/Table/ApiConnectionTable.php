<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Entity\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection;
use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection;

/**
 * DataManager для HL-блока RebitApiConnection.
 */
final class ApiConnectionTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'rebit_api_connection';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            new IntegerField('UF_USER_ID'),
            new StringField('UF_API_KEY_ENCRYPTED'),
            new StringField('UF_SECRET_KEY_ENCRYPTED'),
            new StringField('UF_MODE'),
            new StringField('UF_STATUS'),
            new DatetimeField('UF_LAST_VERIFIED_AT'),
            new StringField('UF_ERROR_MESSAGE'),
            new DatetimeField('UF_CREATED_AT'),
            new DatetimeField('UF_UPDATED_AT'),
        ];
    }

    public static function getObjectClass(): string
    {
        return ApiConnection::class;
    }

    public static function getCollectionClass(): string
    {
        return ApiConnectionCollection::class;
    }
}
