<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Wallet\Presentation\Controller\BalanceController;
use Rebit\Wallet\Presentation\Controller\ReportController;
use Rebit\Wallet\Presentation\Controller\TransactionController;

return static function(RoutingConfigurator $routes) {
    // Балансы
    $routes->get('/api/v1/wallet/balances', [BalanceController::class, 'listAction']);
    $routes->post('/api/v1/wallet/balances/sync', [BalanceController::class, 'syncAction']);

    // Транзакции
    $routes->get('/api/v1/wallet/transactions', [TransactionController::class, 'listAction']);
    $routes->get('/api/v1/wallet/transactions/export', [TransactionController::class, 'exportAction']);

    // Отчёты
    $routes->get('/api/v1/wallet/reports/cash-flow', [ReportController::class, 'cashFlowAction']);
};
