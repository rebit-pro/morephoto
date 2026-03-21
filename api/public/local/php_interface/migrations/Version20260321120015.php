<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120015 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitUserSession (Сессии пользователей)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitUserSession',
            'TABLE_NAME' => 'rebit_user_session',
            'LANG' => [
                'ru' => ['NAME' => 'Сессии пользователей'],
                'en' => ['NAME' => 'User Sessions'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SESSION_ID', 'USER_TYPE_ID' => 'string', 'SORT' => 200, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 128, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_IP_ADDRESS', 'USER_TYPE_ID' => 'string', 'SORT' => 300, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 45, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 45, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_USER_AGENT', 'USER_TYPE_ID' => 'string', 'SORT' => 400, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 2, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 500, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_DEVICE_FINGERPRINT', 'USER_TYPE_ID' => 'string', 'SORT' => 500, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 64, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_IS_ACTIVE', 'USER_TYPE_ID' => 'boolean', 'SORT' => 600, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['DEFAULT_VALUE' => 1, 'DISPLAY' => 'CHECKBOX', 'LABEL' => ['', ''], 'LABEL_CHECKBOX' => '']],
            ['FIELD_NAME' => 'UF_LAST_ACTIVITY_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 700, 'MANDATORY' => 'Y',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N']],
            ['FIELD_NAME' => 'UF_CREATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 800, 'MANDATORY' => 'Y',
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitUserSession');
    }
}
