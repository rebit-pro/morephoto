<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\PhotoOrder\Controller\ApiP2PController;

return static function (RoutingConfigurator $routes) {
    $routes->post('/api/v1/photo/', [ApiP2PController::class, 'handleAction']);
    $routes->post('/api/v1/photo/order', [ApiP2PController::class, 'handleAction']);


};
