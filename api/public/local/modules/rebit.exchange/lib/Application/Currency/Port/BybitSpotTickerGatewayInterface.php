<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\Port;

/**
 * Порт для получения спотовых рыночных цен с Bybit.
 *
 * Используется для расчёта кросс-курсов (например, BTC→USDT).
 * Реализация вызывает публичный Bybit API: GET /v5/market/tickers?category=spot.
 */
interface BybitSpotTickerGatewayInterface
{
    /**
     * Получить последнюю цену спотового рынка для пары.
     *
     * @param string $symbol Символ пары, например 'BTCUSDT', 'USDCUSDT'
     *
     * @return null|float Последняя цена или null при ошибке/отсутствии данных
     */
    public function getLastPrice(string $symbol): ?float;
}
