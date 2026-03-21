<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120014 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitNotificationPreference (Настройки уведомлений)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitNotificationPreference',
            'TABLE_NAME' => 'rebit_notification_preference',
            'LANG' => [
                'ru' => ['NAME' => 'Настройки уведомлений'],
                'en' => ['NAME' => 'Notification Preferences'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CATEGORY', 'USER_TYPE_ID' => 'string', 'SORT' => 200, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_CHANNEL', 'USER_TYPE_ID' => 'string', 'SORT' => 300, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_IS_ENABLED', 'USER_TYPE_ID' => 'boolean', 'SORT' => 400, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['DEFAULT_VALUE' => 1, 'DISPLAY' => 'CHECKBOX', 'LABEL' => ['', ''], 'LABEL_CHECKBOX' => '']],
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitNotificationPreference');
    }
}
