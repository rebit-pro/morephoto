<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Sprint\Migration\Exceptions\HelperException;

class Version20260324120001 extends Version
{
    protected $author = 'auto';

    protected $description = 'Добавление полей Bybit API: rebit_trade_message (content_type, msg_uuid, file_name), rebit_advertisement (premium, payment_period, fee_rate), rebit_trade (bybit_status)';

    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();

        // rebit_trade_message — поля для интеграции с Bybit chat API
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('RebitTradeMessage');
        $fields = [
            [
                'FIELD_NAME' => 'UF_CONTENT_TYPE',
                'USER_TYPE_ID' => 'string',
                'SORT' => 350,
                'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 10, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 10, 'DEFAULT_VALUE' => 'str'],
            ],
            [
                'FIELD_NAME' => 'UF_BYBIT_MSG_UUID',
                'USER_TYPE_ID' => 'string',
                'SORT' => 360,
                'MANDATORY' => 'Y',
                'IS_SEARCHABLE' => 'Y',
                'SETTINGS' => ['SIZE' => 36, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 36, 'DEFAULT_VALUE' => ''],
            ],
            [
                'FIELD_NAME' => 'UF_FILE_NAME',
                'USER_TYPE_ID' => 'string',
                'SORT' => 370,
                'MANDATORY' => 'N',
                'SETTINGS' => ['SIZE' => 60, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 255, 'DEFAULT_VALUE' => ''],
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

        // rebit_advertisement — поля Bybit (premium, payment_period, fee_rate)
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('RebitAdvertisement');
        $fields = [
            [
                'FIELD_NAME' => 'UF_PREMIUM',
                'USER_TYPE_ID' => 'double',
                'SORT' => 650,
                'MANDATORY' => 'N',
                'SETTINGS' => ['PRECISION' => 2, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => ''],
            ],
            [
                'FIELD_NAME' => 'UF_PAYMENT_PERIOD',
                'USER_TYPE_ID' => 'integer',
                'SORT' => 1150,
                'MANDATORY' => 'Y',
                'SETTINGS' => ['SIZE' => 10, 'MIN_VALUE' => 1, 'MAX_VALUE' => 1440, 'DEFAULT_VALUE' => 15],
            ],
            [
                'FIELD_NAME' => 'UF_FEE_RATE',
                'USER_TYPE_ID' => 'double',
                'SORT' => 1160,
                'MANDATORY' => 'N',
                'SETTINGS' => ['PRECISION' => 4, 'SIZE' => 20, 'MIN_VALUE' => 0.0, 'MAX_VALUE' => 0.0, 'DEFAULT_VALUE' => ''],
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

        // rebit_trade — поле для сырого статуса Bybit
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('RebitTrade');
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_BYBIT_STATUS',
            'XML_ID' => 'UF_BYBIT_STATUS',
            'USER_TYPE_ID' => 'integer',
            'SORT' => 150,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'I',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => ['SIZE' => 10, 'MIN_VALUE' => 0, 'MAX_VALUE' => 200, 'DEFAULT_VALUE' => ''],
        ]);
    }

    /**
     * @throws HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('RebitTradeMessage');
        if (0 !== $hlblockId) {
            $helper->Hlblock()->deleteField($hlblockId, 'UF_CONTENT_TYPE');
            $helper->Hlblock()->deleteField($hlblockId, 'UF_BYBIT_MSG_UUID');
            $helper->Hlblock()->deleteField($hlblockId, 'UF_FILE_NAME');
        }

        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('RebitAdvertisement');
        if (0 !== $hlblockId) {
            $helper->Hlblock()->deleteField($hlblockId, 'UF_PREMIUM');
            $helper->Hlblock()->deleteField($hlblockId, 'UF_PAYMENT_PERIOD');
            $helper->Hlblock()->deleteField($hlblockId, 'UF_FEE_RATE');
        }

        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('RebitTrade');
        if (0 !== $hlblockId) {
            $helper->Hlblock()->deleteField($hlblockId, 'UF_BYBIT_STATUS');
        }
    }
}
