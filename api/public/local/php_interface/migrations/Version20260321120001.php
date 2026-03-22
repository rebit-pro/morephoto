<?php

namespace Sprint\Migration;

use Sprint\Migration\Exceptions\HelperException;

class Version20260321120001 extends Version
{
    protected $author = 'auto';

    protected $description = 'Создание HL-блока RebitApiConnection (API подключения к Bybit)';

    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitApiConnection',
            'TABLE_NAME' => 'rebit_api_connection',
            'LANG' => [
                'ru' => ['NAME' => 'API подключения к Bybit'],
                'en' => ['NAME' => 'Bybit API Connections'],
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_USER_ID',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_USER_ID',
            'SORT' => 100,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'SIZE' => 20,
                'MIN_VALUE' => 0,
                'MAX_VALUE' => 0,
                'DEFAULT_VALUE' => '',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_API_KEY_ENCRYPTED',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_API_KEY_ENCRYPTED',
            'SORT' => 200,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'SIZE' => 60,
                'ROWS' => 1,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 512,
                'DEFAULT_VALUE' => '',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_SECRET_KEY_ENCRYPTED',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_SECRET_KEY_ENCRYPTED',
            'SORT' => 300,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'SIZE' => 60,
                'ROWS' => 1,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 512,
                'DEFAULT_VALUE' => '',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_MODE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_MODE',
            'SORT' => 400,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'SIZE' => 10,
                'ROWS' => 1,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 10,
                'DEFAULT_VALUE' => '',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_STATUS',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_STATUS',
            'SORT' => 500,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'SIZE' => 30,
                'ROWS' => 1,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 30,
                'DEFAULT_VALUE' => '',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_LAST_VERIFIED_AT',
            'USER_TYPE_ID' => 'datetime',
            'XML_ID' => 'UF_LAST_VERIFIED_AT',
            'SORT' => 600,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''],
                'USE_SECOND' => 'Y',
                'USE_TIMEZONE' => 'N',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_ERROR_MESSAGE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_ERROR_MESSAGE',
            'SORT' => 700,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'SIZE' => 60,
                'ROWS' => 1,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 500,
                'DEFAULT_VALUE' => '',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_CREATED_AT',
            'USER_TYPE_ID' => 'datetime',
            'XML_ID' => 'UF_CREATED_AT',
            'SORT' => 800,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''],
                'USE_SECOND' => 'Y',
                'USE_TIMEZONE' => 'N',
            ],
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_UPDATED_AT',
            'USER_TYPE_ID' => 'datetime',
            'XML_ID' => 'UF_UPDATED_AT',
            'SORT' => 900,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => [
                'DEFAULT_VALUE' => ['TYPE' => 'NONE', 'VALUE' => ''],
                'USE_SECOND' => 'Y',
                'USE_TIMEZONE' => 'N',
            ],
        ]);
    }

    /**
     * @throws HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();

        $helper->Hlblock()->deleteHlblockIfExists('RebitApiConnection');
    }
}
