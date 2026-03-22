<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Auth\Presentation\Controller\AuthController;

return static function(RoutingConfigurator $routes) {
    $routes->post('/api/v1/auth/login', [AuthController::class, 'loginAction']);
    $routes->post('/api/v1/auth/logout', [AuthController::class, 'logoutAction']);
};
