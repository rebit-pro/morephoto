<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Notification\Presentation\Controller\LeadController;

return static function(RoutingConfigurator $routes) {
    $routes->post('/api/v1/lead', [LeadController::class, 'submitAction']);
    $routes->options('/api/v1/lead', [LeadController::class, 'preflightAction']);
};
