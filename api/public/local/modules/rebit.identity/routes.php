<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Identity\Controller\ApiConnectionController;

return static function(RoutingConfigurator $routes) {
    $routes->post('/api/v1/identity/connection', [ApiConnectionController::class, 'connectAction']);
    $routes->delete('/api/v1/identity/connection', [ApiConnectionController::class, 'disconnectAction']);
    $routes->post('/api/v1/identity/connection/verify', [ApiConnectionController::class, 'verifyAction']);
    $routes->get('/api/v1/identity/connection/status', [ApiConnectionController::class, 'statusAction']);
};
