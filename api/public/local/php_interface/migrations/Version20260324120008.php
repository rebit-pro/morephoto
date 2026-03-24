<?php
declare(strict_types=1);
namespace Sprint\Migration;
use Bitrix\Main\Application;
use Sprint\Migration\Exceptions\HelperException;
/**
 * Добавляет поле UF_BYBIT_ID в rebit_payment_method для маппинга
 * числовых ID платёжных методов Bybit (из поля item.payments в API)
 * на локальные записи справочника.
 *
 * Без этого поля невозможно сопоставить IDs из rebit_order_book.UF_PAYMENT_METHOD_IDS
 * с человекочитаемыми названиями способов оплаты.
 */
class Version20260324120008 extends Version
{
    protected $author = 'auto';
    protected $description = 'Добавление UF_BYBIT_ID в rebit_payment_method + заполнение маппинга Bybit ID → способ оплаты';
    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('RebitPaymentMethod');
        $helper->Hlblock()->saveField($hlblockId, [
            'FIELD_NAME'   => 'UF_BYBIT_ID',
            'USER_TYPE_ID' => 'integer',
            'XML_ID'       => 'UF_BYBIT_ID',
            'SORT'         => 50,
            'MULTIPLE'     => 'N',
            'MANDATORY'    => 'N',
            'SHOW_FILTER'  => 'I',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS'     => ['SIZE' => 10, 'MIN_VALUE' => 0, 'MAX_VALUE' => 0, 'DEFAULT_VALUE' => ''],
        ]);
        // Уникальный индекс: один Bybit ID — один способ оплаты
        // Nullable, т.к. у некоторых методов Bybit ID может быть неизвестен
        Application::getConnection()->queryExecute(
            'ALTER TABLE rebit_payment_method ADD UNIQUE INDEX ux_payment_method_bybit_id (UF_BYBIT_ID)',
        );
        // Заполняем известные маппинги Bybit payment method ID -> UF_CODE
        // Источник: реальные ответы Bybit P2P API (поле item.payments в /v5/p2p/item/online)
        $knownMappings = [
            14  => 'SBP',
            18  => 'TINKOFF',
            40  => 'SBERBANK',
            75  => 'RAIFFEISEN',
            64  => 'ALFA',
            90  => 'YUMONEY',
        ];
        $connection = Application::getConnection();
        $helper2    = $connection->getSqlHelper();
        foreach ($knownMappings as $bybitId => $code) {
            $safeCode = $helper2->forSql($code);
            $connection->queryExecute(
                "UPDATE rebit_payment_method SET UF_BYBIT_ID = {$bybitId} WHERE UF_CODE = '{$safeCode}'",
            );
        }
    }
    /**
     * @throws HelperException
     */
    public function down(): void
    {
        $connection = Application::getConnection();
        $connection->queryExecute(
            'ALTER TABLE rebit_payment_method DROP INDEX ux_payment_method_bybit_id',
        );
        $helper     = $this->getHelperManager();
        $hlblockId  = $helper->Hlblock()->getHlblockIdIfExists('RebitPaymentMethod');
        if (0 !== $hlblockId) {
            $helper->Hlblock()->deleteField($hlblockId, 'UF_BYBIT_ID');
        }
    }
}
