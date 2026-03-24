<?php
declare(strict_types=1);
namespace Sprint\Migration;
use Bitrix\Main\Application;
/**
 * Добавляет уникальный индекс на UF_CODE в rebit_currency,
 * чтобы предотвратить дублирование валют при повторных запусках seed-миграции.
 */
class Version20260324120007 extends Version
{
    protected $author = 'auto';
    protected $description = 'Уникальный индекс UF_CODE в rebit_currency';
    public function up(): void
    {
        $connection = Application::getConnection();
        // Удаляем возможные дубли перед установкой индекса
        $connection->queryExecute(
            'DELETE t1 FROM rebit_currency t1
             INNER JOIN rebit_currency t2
             WHERE t1.ID > t2.ID AND t1.UF_CODE = t2.UF_CODE',
        );
        // UF_CODE — TEXT-колонка, поэтому явно задаём длину ключа (10 символов = MAX_LENGTH поля)
        $connection->queryExecute(
            'ALTER TABLE rebit_currency ADD UNIQUE INDEX ux_currency_code (UF_CODE(10))',
        );
    }
    public function down(): void
    {
        Application::getConnection()->queryExecute(
            'ALTER TABLE rebit_currency DROP INDEX ux_currency_code',
        );
    }
}
