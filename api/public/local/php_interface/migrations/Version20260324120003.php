<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Bitrix\Main\Application;

/**
 * Начальное заполнение справочника способов оплаты.
 *
 * Вставка идемпотентна: INSERT IGNORE гарантирует, что повторный запуск
 * не приведёт к дублированию записей.
 */
class Version20260324120003 extends Version
{
    protected $author = 'auto';

    protected $description = 'Seed: заполнение реестра способов оплаты (rebit_payment_method)';

    public function up(): void
    {
        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();

        /** @var array<int, array{code: string, name: string, sort: int}> $methods */
        $methods = [
            ['code' => 'SBP', 'name' => 'СБП', 'sort' => 10],
            ['code' => 'TINKOFF', 'name' => 'Tinkoff', 'sort' => 20],
            ['code' => 'SBERBANK', 'name' => 'Сбербанк', 'sort' => 30],
            ['code' => 'RAIFFEISEN', 'name' => 'Райффайзен', 'sort' => 40],
            ['code' => 'YUMONEY', 'name' => 'ЮMoney', 'sort' => 50],
            ['code' => 'GAZPROM', 'name' => 'Газпромбанк', 'sort' => 60],
            ['code' => 'VTB', 'name' => 'ВТБ', 'sort' => 70],
            ['code' => 'ALFA', 'name' => 'Альфа-Банк', 'sort' => 80],
            ['code' => 'CASH', 'name' => 'Наличные', 'sort' => 100],
        ];

        foreach ($methods as $method) {
            $code = $helper->forSql($method['code']);
            $name = $helper->forSql($method['name']);
            $sort = (int)$method['sort'];

            $connection->queryExecute(
                'INSERT IGNORE INTO rebit_payment_method (UF_CODE, UF_NAME, UF_IS_ACTIVE, UF_SORT)'
                . " VALUES ('{$code}', '{$name}', 1, {$sort})",
            );
        }
    }

    public function down(): void
    {
        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();

        $codes = implode(',', array_map(
            static fn(string $c): string => "'" . $helper->forSql($c) . "'",
            ['SBP', 'TINKOFF', 'SBERBANK', 'RAIFFEISEN', 'YUMONEY', 'GAZPROM', 'VTB', 'ALFA', 'CASH'],
        ));

        $connection->queryExecute(
            "DELETE FROM rebit_payment_method WHERE UF_CODE IN ({$codes})",
        );
    }
}
