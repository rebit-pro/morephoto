<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120013 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitNotification (Уведомления)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitNotification',
            'TABLE_NAME' => 'rebit_notification',
            'LANG' => [
                'ru' => ['NAME' => 'Уведомления'],
                'en' => ['NAME' => 'Notifications'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CATEGORY', 'USER_TYPE_ID' => 'string', 'SORT' => 200, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_TYPE', 'USER_TYPE_ID' => 'string', 'SORT' => 300, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 50, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 50, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_TITLE', 'USER_TYPE_ID' => 'string', 'SORT' => 400, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 255, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_MESSAGE', 'USER_TYPE_ID' => 'string', 'SORT' => 500, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 5, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_ENTITY_TYPE', 'USER_TYPE_ID' => 'string', 'SORT' => 600, 'MANDATORY' => 'N', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 30, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 30, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_ENTITY_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 700, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_IS_READ', 'USER_TYPE_ID' => 'boolean', 'SORT' => 800, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['DEFAULT_VALUE' => 0, 'DISPLAY' => 'CHECKBOX', 'LABEL' => ['', ''], 'LABEL_CHECKBOX' => '']],
            ['FIELD_NAME' => 'UF_CHANNELS', 'USER_TYPE_ID' => 'string', 'SORT' => 900, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 100, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CREATED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 1000, 'MANDATORY' => 'Y',
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitNotification');
    }
}
