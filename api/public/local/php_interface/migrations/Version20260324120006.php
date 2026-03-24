<?php
declare(strict_types=1);
namespace Sprint\Migration;
use Bitrix\Main\Application;
/**
 * Начальное заполнение справочника валют.
 *
 * Вставка идемпотентна: INSERT IGNORE гарантирует, что повторный запуск
 * не приведёт к дублированию записей.
 */
class Version20260324120006 extends Version
{
    protected $author = 'auto';
    protected $description = 'Seed: заполнение справочника валют (rebit_currency)';
    public function up(): void
    {
        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();
        /**
         * @var array<int, array{
         *     code: string,
         *     name: string,
         *     type: string,
         *     decimals: int,
         *     is_active: int,
         *     sort: int,
         * }> $currencies
         */
        $currencies = [
            ['code' => 'USDT', 'name' => 'Tether USD',          'type' => 'crypto', 'decimals' => 2, 'is_active' => 1, 'sort' => 10],
            ['code' => 'USDC', 'name' => 'USD Coin',             'type' => 'crypto', 'decimals' => 2, 'is_active' => 1, 'sort' => 20],
            ['code' => 'BTC',  'name' => 'Bitcoin',              'type' => 'crypto', 'decimals' => 8, 'is_active' => 1, 'sort' => 30],
            ['code' => 'RUB',  'name' => 'Российский рубль',     'type' => 'fiat',   'decimals' => 2, 'is_active' => 1, 'sort' => 100],
        ];
        foreach ($currencies as $currency) {
            $code     = $helper->forSql($currency['code']);
            $name     = $helper->forSql($currency['name']);
            $type     = $helper->forSql($currency['type']);
            $decimals = (int)$currency['decimals'];
            $isActive = (int)$currency['is_active'];
            $sort     = (int)$currency['sort'];
            $connection->queryExecute(
                'INSERT IGNORE INTO rebit_currency (UF_CODE, UF_NAME, UF_TYPE, UF_DECIMALS, UF_IS_ACTIVE, UF_SORT)'
                . " VALUES ('{$code}', '{$name}', '{$type}', {$decimals}, {$isActive}, {$sort})",
            );
        }
    }
    public function down(): void
    {
        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();
        $codes = implode(',', array_map(
            static fn(string $c): string => "'" . $helper->forSql($c) . "'",
            ['USDT', 'USDC', 'BTC', 'RUB'],
        ));
        $connection->queryExecute(
            "DELETE FROM rebit_currency WHERE UF_CODE IN ({$codes})",
        );
    }
}
