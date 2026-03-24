<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Bitrix\Main\Application;

/**
 * Добавляет уникальный индекс на UF_CODE в rebit_payment_method,
 * чтобы предотвратить дублирование способов оплаты при повторных запусках seed-миграции.
 */
class Version20260324120004 extends Version
{
    protected $author = 'auto';

    protected $description = 'Уникальный индекс UF_CODE в rebit_payment_method';

    public function up(): void
    {
        $connection = Application::getConnection();

        // Удаляем возможные дубли перед установкой индекса
        $connection->queryExecute(
            'DELETE t1 FROM rebit_payment_method t1
             INNER JOIN rebit_payment_method t2
             WHERE t1.ID > t2.ID AND t1.UF_CODE = t2.UF_CODE',
        );

        // UF_CODE — TEXT-колонка, поэтому явно задаём длину ключа (50 символов = MAX_LENGTH поля)
        $connection->queryExecute(
            'ALTER TABLE rebit_payment_method ADD UNIQUE INDEX ux_payment_method_code (UF_CODE(50))',
        );
    }

    public function down(): void
    {
        Application::getConnection()->queryExecute(
            'ALTER TABLE rebit_payment_method DROP INDEX ux_payment_method_code',
        );
    }
}
