<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\PhotoOrder\Controller\PhotoController;

return static function (RoutingConfigurator $routes) {
    $routes->post('/api/v1/photo/', [PhotoController::class, 'handleAction']);
    $routes->post('/api/v1/photo/order', [PhotoController::class, 'handleAction']);


};
