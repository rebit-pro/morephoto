<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120007 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitTradeChatScriptStep (Шаги скрипта чата)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitTradeChatScriptStep',
            'TABLE_NAME' => 'rebit_trade_chat_script_step',
            'LANG' => [
                'ru' => ['NAME' => 'Шаги скрипта чата'],
                'en' => ['NAME' => 'Trade Chat Script Steps'],
            ],
        ]);
        $fields = [
            ['FIELD_NAME' => 'UF_SCRIPT_ID', 'USER_TYPE_ID' => 'integer', 'SORT' => 100, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_SORT', 'USER_TYPE_ID' => 'integer', 'SORT' => 200, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => 100]],
            ['FIELD_NAME' => 'UF_MESSAGE', 'USER_TYPE_ID' => 'string', 'SORT' => 300, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 5, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 0, 'DEFAULT_VALUE' => '']],
            ['FIELD_NAME' => 'UF_DELAY_SECONDS', 'USER_TYPE_ID' => 'integer', 'SORT' => 400, 'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => 0]],
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitTradeChatScriptStep');
    }
}
