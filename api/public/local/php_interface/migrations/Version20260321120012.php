<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120012 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitTransaction (Транзакции)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitTransaction',
            'TABLE_NAME' => 'rebit_transaction',
            'LANG' => [
                'ru' => ['NAME' => 'Транзакции'],
                'en' => ['NAME' => 'Transactions'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CURRENCY_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 200, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_TYPE', 'USER_TYPE_ID' => 'string', 'SORT' => 300, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_AMOUNT', 'USER_TYPE_ID' => 'double', 'SORT' => 400, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_BALANCE_AFTER', 'USER_TYPE_ID' => 'double', 'SORT' => 500, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_TRADE_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 600, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_DESCRIPTION', 'USER_TYPE_ID' => 'string', 'SORT' => 700, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 500, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_BYBIT_TX_ID', 'USER_TYPE_ID' => 'string', 'SORT' => 800, 'MANDATORY' => 'N', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 64, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CREATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 900, 'MANDATORY' => 'Y',
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitTransaction');
    }
}
