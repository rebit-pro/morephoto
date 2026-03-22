<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120017 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitSecurityAlert (Алерты безопасности)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitSecurityAlert',
            'TABLE_NAME' => 'rebit_security_alert',
            'LANG' => [
                'ru' => ['NAME' => 'Алерты безопасности'],
                'en' => ['NAME' => 'Security Alerts'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_USER_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_ALERT_TYPE', 'USER_TYPE_ID' => 'string', 'SORT' => 200, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 40, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 40, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SEVERITY', 'USER_TYPE_ID' => 'string', 'SORT' => 300, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 10, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 10, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_STATUS', 'USER_TYPE_ID' => 'string', 'SORT' => 400, 'MANDATORY' => 'Y', 'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_DESCRIPTION', 'USER_TYPE_ID' => 'string', 'SORT' => 500, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 5, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_PAYLOAD', 'USER_TYPE_ID' => 'string', 'SORT' => 600, 'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 5, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_RESOLVED_AT', 'USER_TYPE_ID' => 'datetime', 'SORT' => 700, 'MANDATORY' => 'N',
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitSecurityAlert');
    }
}
