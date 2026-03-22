<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120011 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitBalance (Балансы пользователей)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitBalance',
            'TABLE_NAME' => 'rebit_balance',
            'LANG' => [
                'ru' => ['NAME' => 'Балансы пользователей'],
                'en' => ['NAME' => 'User Balances'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CURRENCY_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 200, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_AVAILABLE', 'USER_TYPE_ID' => 'double', 'SORT' => 300, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => 0]],
            ['FIELD_NAME' => 'UF_LOCKED', 'USER_TYPE_ID' => 'double', 'SORT' => 400, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => 0]],
            ['FIELD_NAME' => 'UF_TOTAL', 'USER_TYPE_ID' => 'double', 'SORT' => 500, 'MANDATORY' => 'Y',
                'SETTINGS' => ['PRECISION' => 8, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => 0]],
            ['FIELD_NAME' => 'UF_SYNCED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 600, 'MANDATORY' => 'Y',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_UPDATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 700, 'MANDATORY' => 'Y',
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitBalance');
    }
}
