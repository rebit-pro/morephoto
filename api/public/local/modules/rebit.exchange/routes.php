<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Exchange\Presentation\Controller\AdvertisementController;
use Rebit\Exchange\Presentation\Controller\ChatScriptController;
use Rebit\Exchange\Presentation\Controller\CurrencyController;
use Rebit\Exchange\Presentation\Controller\OrderBookController;
use Rebit\Exchange\Presentation\Controller\PaymentMethodController;
use Rebit\Exchange\Presentation\Controller\TradeChatController;
use Rebit\Exchange\Presentation\Controller\TradeController;

return static function(RoutingConfigurator $routes) {
    // Справочники
    $routes->get('/api/v1/exchange/currencies', [CurrencyController::class, 'currenciesAction']);
    $routes->get('/api/v1/exchange/currency-pairs', [CurrencyController::class, 'pairsAction']);
    $routes->get('/api/v1/exchange/payment-methods', [PaymentMethodController::class, 'listAction']);

    // Стакан ордеров
    $routes->get('/api/v1/exchange/orderbook', [OrderBookController::class, 'listAction']);

    // Объявления
    $routes->get('/api/v1/exchange/advertisements', [AdvertisementController::class, 'listAction']);
    $routes->post('/api/v1/exchange/advertisements', [AdvertisementController::class, 'createAction']);
    $routes->patch('/api/v1/exchange/advertisements/{id}', [AdvertisementController::class, 'toggleAction']);
    $routes->delete('/api/v1/exchange/advertisements/{id}', [AdvertisementController::class, 'deleteAction']);

    // Сделки
    $routes->get('/api/v1/exchange/trades', [TradeController::class, 'listAction']);
    $routes->get('/api/v1/exchange/trades/{id}', [TradeController::class, 'detailAction']);
    $routes->post('/api/v1/exchange/trades/{id}/pay', [TradeController::class, 'payAction']);
    $routes->post('/api/v1/exchange/trades/{id}/release', [TradeController::class, 'releaseAction']);

    // Чат сделки
    $routes->get('/api/v1/exchange/trades/{tradeId}/chat', [TradeChatController::class, 'historyAction']);
    $routes->post('/api/v1/exchange/trades/{tradeId}/chat', [TradeChatController::class, 'sendAction']);

    // Скрипты автосообщений
    $routes->get('/api/v1/exchange/chat-scripts', [ChatScriptController::class, 'listAction']);
    $routes->post('/api/v1/exchange/chat-scripts', [ChatScriptController::class, 'createAction']);
    $routes->patch('/api/v1/exchange/chat-scripts/{id}', [ChatScriptController::class, 'updateAction']);
    $routes->delete('/api/v1/exchange/chat-scripts/{id}', [ChatScriptController::class, 'deleteAction']);
};
