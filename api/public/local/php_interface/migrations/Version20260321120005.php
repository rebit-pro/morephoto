<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120005 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitOrderBook (Стакан ордеров)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitOrderBook',
            'TABLE_NAME' => 'rebit_order_book',
            'LANG' => [
                'ru' => ['NAME' => 'Стакан ордеров'],
                'en' => ['NAME' => 'Order Book'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_BYBIT_ORDER_ID', 'USER_TYPE_ID' => 'string', 'SORT' => 100, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I', 'IS_SEARCHABLE' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 64, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CURRENCY_PAIR_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 200, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SIDE', 'USER_TYPE_ID' => 'string', 'SORT' => 300, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 4, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 4, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PRICE', 'USER_TYPE_ID' => 'double', 'SORT' => 400, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_QUANTITY', 'USER_TYPE_ID' => 'double', 'SORT' => 500, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_MIN_AMOUNT', 'USER_TYPE_ID' => 'double', 'SORT' => 600, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_MAX_AMOUNT', 'USER_TYPE_ID' => 'double', 'SORT' => 700, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_COUNTERPARTY_NAME', 'USER_TYPE_ID' => 'string', 'SORT' => 800, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 100, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_COUNTERPARTY_RATING', 'USER_TYPE_ID' => 'double', 'SORT' => 900, 'MANDATORY' => 'N',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_COUNTERPARTY_TRADES', 'USER_TYPE_ID' => 'integer', 'SORT' => 1000, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_COUNTERPARTY_COMPLETION_RATE', 'USER_TYPE_ID' => 'double', 'SORT' => 1100, 'MANDATORY' => 'N',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PAYMENT_METHOD_IDS', 'USER_TYPE_ID' => 'string', 'SORT' => 1200, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 255, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PAYMENT_TIME_LIMIT', 'USER_TYPE_ID' => 'integer', 'SORT' => 1300, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SYNCED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1400, 'MANDATORY' => 'Y',
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitOrderBook');
    }
}
