<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120009 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitTrade (Сделки)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitTrade',
            'TABLE_NAME' => 'rebit_trade',
            'LANG' => [
                'ru' => ['NAME' => 'Сделки'],
                'en' => ['NAME' => 'Trades'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_BYBIT_ORDER_ID', 'USER_TYPE_ID' => 'string', 'SORT' => 100, 'MANDATORY' => 'N', 'SHOW_FILTER' => 'I', 'IS_SEARCHABLE' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 64, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_BUYER_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 200, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SELLER_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 300, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_ADVERTISEMENT_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 400, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_ORDER_BOOK_ENTRY_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 500, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CURRENCY_PAIR_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 600, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SIDE', 'USER_TYPE_ID' => 'string', 'SORT' => 700, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 4, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 4, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PRICE', 'USER_TYPE_ID' => 'double', 'SORT' => 800, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_QUANTITY', 'USER_TYPE_ID' => 'double', 'SORT' => 900, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_FIAT_AMOUNT', 'USER_TYPE_ID' => 'double', 'SORT' => 1000, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_FEE', 'USER_TYPE_ID' => 'double', 'SORT' => 1100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PAYMENT_METHOD_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 1200, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PAYMENT_DETAILS', 'USER_TYPE_ID' => 'string', 'SORT' => 1300, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 5, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_COMMENT', 'USER_TYPE_ID' => 'string', 'SORT' => 1400, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 5, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_STATUS', 'USER_TYPE_ID' => 'string', 'SORT' => 1500, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PAYMENT_DEADLINE', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1600, 'MANDATORY' => 'N',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_PAID_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1700, 'MANDATORY' => 'N',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_CONFIRMED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1800, 'MANDATORY' => 'N',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_COMPLETED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1900, 'MANDATORY' => 'N',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_CANCELLED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 2000, 'MANDATORY' => 'N',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_CANCEL_REASON', 'USER_TYPE_ID' => 'string', 'SORT' => 2100, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 50, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 50, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_COUNTERPARTY_NAME', 'USER_TYPE_ID' => 'string', 'SORT' => 2200, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 100, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CREATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 2300, 'MANDATORY' => 'Y',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_UPDATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 2400, 'MANDATORY' => 'Y',
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitTrade');
    }
}
