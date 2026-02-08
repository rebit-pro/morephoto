<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Bybit\Controller\ApiAdsController;

return static function(RoutingConfigurator $routes) {
    $routes->get('/api/v1/bybit/Ads/', [ApiAdsController::class, 'GetAdsAction']);
    $routes->post('/api/v1/bybit/Ads/', [ApiAdsController::class, 'CreateAdsAction']);
    $routes->post('/api/v1/bybit/Ads/cancel/', [ApiAdsController::class, 'CancelAdAction']);
    $routes->post('/api/v1/bybit/Ads/update/', [ApiAdsController::class, 'UpdateAdAction']);
    $routes->get('/api/v1/bybit/Ads/personal/list/', [ApiAdsController::class, 'PersonalListAction']);
    $routes->get('/api/v1/bybit/Ads/info/', [ApiAdsController::class, 'AdInfoAction']);
};
