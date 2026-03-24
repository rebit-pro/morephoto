<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Sprint\Migration\Exceptions\HelperException;

class Version20260324120002 extends Version
{
    protected $author = 'auto';

    protected $description = 'Создание HL-блока RebitChatScriptExecution (Очередь исполнения чат-скриптов)';

    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitChatScriptExecution',
            'TABLE_NAME' => 'rebit_chat_script_execution',
            'LANG' => [
                'ru' => ['NAME' => 'Очередь исполнения чат-скриптов'],
                'en' => ['NAME' => 'Chat Script Execution Queue'],
            ],
        ]);

        $fields = [
            [
                'FIELD_NAME' => 'UF_TRADE_ID',
                'USER_TYPE_ID' => 'integer',
                'SORT' => 100,
                'MANDATORY' => 'Y',
                'IS_SEARCHABLE' => 'Y',
                'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => ''],
            ],
            [
                'FIELD_NAME' => 'UF_SCRIPT_ID',
                'USER_TYPE_ID' => 'integer',
                'SORT' => 200,
                'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => ''],
            ],
            [
                'FIELD_NAME' => 'UF_USER_ID',
                'USER_TYPE_ID' => 'integer',
                'SORT' => 300,
                'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => ''],
            ],
            [
                'FIELD_NAME' => 'UF_LAST_STEP_SORT',
                'USER_TYPE_ID' => 'integer',
                'SORT' => 400,
                'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 10, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => 0],
            ],
            [
                'FIELD_NAME' => 'UF_STATUS',
                'USER_TYPE_ID' => 'string',
                'SORT' => 500,
                'MANDATORY' => 'Y',
                'IS_SEARCHABLE' => 'Y',
                'SHOW_FILTER' => 'I',
                'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => 'pending'],
            ],
            [
                'FIELD_NAME' => 'UF_NEXT_RUN_AT',
                'USER_TYPE_ID' => 'datetime',
                'SORT' => 600,
                'MANDATORY' => 'Y',
                'IS_SEARCHABLE' => 'Y',
                'SHOW_FILTER' => 'I',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N'],
            ],
            [
                'FIELD_NAME' => 'UF_CREATED_AT',
                'USER_TYPE_ID' => 'datetime',
                'SORT' => 700,
                'MANDATORY' => 'Y',
                'SETTINGS' => ['DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''], 'USE_SECOND' => 'Y', 'USE_TIMEZONE' => 'N'],
            ],
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
        $helper->Hlblock()->deleteHlblockIfExists('RebitChatScriptExecution');
    }
}
