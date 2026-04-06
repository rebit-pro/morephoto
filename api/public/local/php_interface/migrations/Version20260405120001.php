<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Bitrix\Main\Application;

/**
 * Повторное заполнение таблицы валютных пар.
 *
 * Version20260324120005 выполнялась до Version20260324120006 (seed валют),
 * поэтому таблица rebit_currency была пуста и все пары были пропущены.
 * Эта миграция повторяет логику идемпотентно (INSERT IGNORE).
 */
class Version20260405120001 extends Version
{
    protected $author = 'auto';

    protected $description = 'Fix: повторное заполнение валютных пар (rebit_currency_pair) после seed валют';

    public function up(): void
    {
        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();

        // Загружаем ID валют по кодам
        /** @var array<string, int> $currencyIds */
        $currencyIds = [];
        $result = $connection->query('SELECT ID, UF_CODE FROM rebit_currency');
        while ($row = $result->fetch()) {
            $currencyIds[$row['UF_CODE']] = (int)$row['ID'];
        }

        /** @var array<int, array{
         *     token: string,
         *     fiat: string,
         *     code: string,
         *     is_active: int,
         *     is_default: int,
         *     sort: int,
         * }> $pairs */
        $pairs = [
            [
                'token' => 'USDT',
                'fiat' => 'RUB',
                'code' => 'USDT_RUB',
                'is_active' => 1,
                'is_default' => 1,
                'sort' => 10,
            ],
            [
                'token' => 'BTC',
                'fiat' => 'RUB',
                'code' => 'BTC_RUB',
                'is_active' => 1,
                'is_default' => 0,
                'sort' => 20,
            ],
            [
                'token' => 'USDC',
                'fiat' => 'RUB',
                'code' => 'USDC_RUB',
                'is_active' => 1,
                'is_default' => 0,
                'sort' => 30,
            ],
        ];

        foreach ($pairs as $pair) {
            $tokenId = $currencyIds[$pair['token']] ?? null;
            $fiatId = $currencyIds[$pair['fiat']] ?? null;

            if (null === $tokenId || null === $fiatId) {
                $this->outWarning('Валюта не найдена, пара пропущена: ' . $pair['code']);
                continue;
            }

            $code = $helper->forSql($pair['code']);
            $isActive = (int)$pair['is_active'];
            $isDefault = (int)$pair['is_default'];
            $sort = (int)$pair['sort'];

            $connection->queryExecute(
                'INSERT IGNORE INTO rebit_currency_pair'
                . ' (UF_TOKEN_CURRENCY_ID, UF_FIAT_CURRENCY_ID, UF_CODE, UF_IS_ACTIVE, UF_IS_DEFAULT, UF_SORT)'
                . " VALUES ({$tokenId}, {$fiatId}, '{$code}', {$isActive}, {$isDefault}, {$sort})",
            );
        }

        // Гарантируем, что только одна пара является дефолтной
        $connection->queryExecute(
            "UPDATE rebit_currency_pair SET UF_IS_DEFAULT = 0 WHERE UF_CODE != 'USDT_RUB'",
        );
    }

    public function down(): void
    {
        // down не нужен — данные были в Version20260324120005.down()
    }
}
