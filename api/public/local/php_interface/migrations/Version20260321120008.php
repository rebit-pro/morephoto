<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120008 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitAdvertisement (Объявления)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitAdvertisement',
            'TABLE_NAME' => 'rebit_advertisement',
            'LANG' => [
                'ru' => ['NAME' => 'Объявления'],
                'en' => ['NAME' => 'Advertisements'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_BYBIT_AD_ID', 'USER_TYPE_ID' => 'string', 'SORT' => 200, 'MANDATORY' => 'N', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 64, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CURRENCY_PAIR_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 300, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SIDE', 'USER_TYPE_ID' => 'string', 'SORT' => 400, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 4, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 4, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PRICE_TYPE', 'USER_TYPE_ID' => 'string', 'SORT' => 500, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 10, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 10, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PRICE', 'USER_TYPE_ID' => 'double', 'SORT' => 600, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_QUANTITY', 'USER_TYPE_ID' => 'double', 'SORT' => 700, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_QUANTITY_REMAINING', 'USER_TYPE_ID' => 'double', 'SORT' => 800, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_MIN_AMOUNT', 'USER_TYPE_ID' => 'double', 'SORT' => 900, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_MAX_AMOUNT', 'USER_TYPE_ID' => 'double', 'SORT' => 1000, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PAYMENT_METHOD_IDS', 'USER_TYPE_ID' => 'string', 'SORT' => 1100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 255, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CONDITIONS', 'USER_TYPE_ID' => 'string', 'SORT' => 1200, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 5, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CHAT_SCRIPT_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 1300, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_STATUS', 'USER_TYPE_ID' => 'string', 'SORT' => 1400, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CREATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1500, 'MANDATORY' => 'Y',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_UPDATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1600, 'MANDATORY' => 'Y',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
        ];
        foreach ($fields as $field) {
            $helper->Hlblock()->saveField($hlblockId, array_merge([
                'XML_ID' => $field['FIELD_NAME'],
                'MULTIPLE' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N',
            ], $field));
        }
    }
    /**
     * @throws HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('RebitAdvertisement');
    }
}
