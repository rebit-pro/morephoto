<?php
namespace Sprint\Migration;
use Sprint\Migration\Exceptions\HelperException;
class Version20260321120003 extends Version
{
    protected $author = 'auto';
    protected $description = 'Создание HL-блока RebitCurrencyPair (Валютные пары)';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'RebitCurrencyPair',
            'TABLE_NAME' => 'rebit_currency_pair',
            'LANG' => [
                'ru' => ['NAME' => 'Валютные пары'],
                'en' => ['NAME' => 'Currency Pairs'],
            ],
        ]);
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_TOKEN_CURRENCY_ID',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_TOKEN_CURRENCY_ID',
            'SORT' => 100,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => ''],
        ]);
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_FIAT_CURRENCY_ID',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_FIAT_CURRENCY_ID',
            'SORT' => 200,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => ''],
        ]);
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_CODE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_CODE',
            'SORT' => 300,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'I',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'Y',
            'SETTINGS' => ['SIZE' => 20, 'ROWS' => 1, 'REGEXP' => '', 'MIN_LENGTH' => 0, 'MAX_LENGTH' => 20, 'DEFAULT_VALUE' => ''],
        ]);
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_IS_ACTIVE',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => 'UF_IS_ACTIVE',
            'SORT' => 400,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'I',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => ['DEFAULT_VALUE' => 1, 'DISPLAY' => 'CHECKBOX', 'LABEL' => ['', ''], 'LABEL_CHECKBOX' => ''],
        ]);
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_IS_DEFAULT',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => 'UF_IS_DEFAULT',
            'SORT' => 500,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'I',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => ['DEFAULT_VALUE' => 0, 'DISPLAY' => 'CHECKBOX', 'LABEL' => ['', ''], 'LABEL_CHECKBOX' => ''],
        ]);
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME' => 'UF_SORT',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_SORT',
            'SORT' => 600,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'Y',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => ['SIZE' => 20, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => 500],
        ]);
    }
    /**
     * @throws HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('RebitCurrencyPair');
    }
}
