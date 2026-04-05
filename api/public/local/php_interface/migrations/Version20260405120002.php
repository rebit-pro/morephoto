<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Bitrix\Main\Application;

/**
 * Исправление PRECISION UF-полей типа double во всех HL-блоках проекта.
 *
 * Миграции создания HL-блоков задавали PRECISION=8, но фактические настройки
 * UF-полей могут отличаться (Sprint Migration saveField может некорректно
 * применять SETTINGS). Это приводит к округлению значений обработчиком
 * CUserTypeDouble::OnBeforeSave.
 *
 * Эта миграция явно обновляет SETTINGS в b_user_field для всех double-полей
 * указанных таблиц, гарантируя PRECISION=8.
 */
class Version20260405120002 extends Version
{
    protected $author = 'auto';

    protected $description = 'Fix: PRECISION=8 для всех double UF-полей HL-блоков';

    /** @var array<string, list<string>> tableName → fieldNames */
    private const array FIELDS_MAP = [
        'rebit_balance' => ['UF_AVAILABLE', 'UF_LOCKED', 'UF_TOTAL'],
        'rebit_transaction' => ['UF_AMOUNT', 'UF_BALANCE_AFTER'],
        'rebit_order_book' => [
            'UF_PRICE', 'UF_QUANTITY', 'UF_MIN_AMOUNT', 'UF_MAX_AMOUNT',
            'UF_COUNTERPARTY_RATING', 'UF_COUNTERPARTY_COMPLETION_RATE',
        ],
        'rebit_advertisement' => [
            'UF_PRICE', 'UF_PREMIUM', 'UF_QUANTITY', 'UF_QUANTITY_REMAINING',
            'UF_MIN_AMOUNT', 'UF_MAX_AMOUNT', 'UF_FEE_RATE',
        ],
        'rebit_trade' => ['UF_PRICE', 'UF_QUANTITY', 'UF_FIAT_AMOUNT', 'UF_FEE'],
    ];

    private const int TARGET_PRECISION = 8;

    public function up(): void
    {
        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();

        foreach (self::FIELDS_MAP as $tableName => $fieldNames) {
            $this->out("--- {$tableName} ---");

            foreach ($fieldNames as $fieldName) {
                $fieldNameSql = $helper->forSql($fieldName);
                $tableNameSql = $helper->forSql($tableName);

                $row = $connection->query(
                    "SELECT uf.ID, uf.SETTINGS
                     FROM b_user_field uf
                     INNER JOIN b_hlblock_entity hl ON CONCAT('HLBLOCK_', hl.ID) = uf.ENTITY_ID
                     WHERE hl.TABLE_NAME = '{$tableNameSql}'
                       AND uf.FIELD_NAME = '{$fieldNameSql}'",
                )->fetch();

                if (false === $row) {
                    $this->outWarning("{$fieldName}: не найдено, пропуск");
                    continue;
                }

                $settings = unserialize($row['SETTINGS'], ['allowed_classes' => false]);
                $currentPrecision = (int)($settings['PRECISION'] ?? 0);

                if (self::TARGET_PRECISION === $currentPrecision) {
                    $this->out("{$fieldName}: PRECISION уже " . self::TARGET_PRECISION);
                    continue;
                }

                $this->out("{$fieldName}: PRECISION={$currentPrecision} → " . self::TARGET_PRECISION);

                $settings['PRECISION'] = self::TARGET_PRECISION;
                $serialized = $helper->forSql(serialize($settings));

                $connection->queryExecute(
                    "UPDATE b_user_field SET SETTINGS = '{$serialized}' WHERE ID = {$row['ID']}",
                );
            }
        }

        // Очистка кеша UF-полей после изменения настроек
        $managedCache = Application::getInstance()->getManagedCache();
        $managedCache->cleanDir('b_user_field');

        $this->out('Кеш UF-полей очищен');
    }

    public function down(): void
    {
        // Откат не требуется — корректное значение PRECISION
    }
}
