<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Bybit\Infrastructure\Http\ByBitHttpClient;
use Rebit\Bybit\Infrastructure\Http\Config\ByBitConfig;
use Rebit\Bybit\Infrastructure\Http\Contract\RequestIdGeneratorInterface;
use Rebit\Bybit\Infrastructure\Http\Generator\UuidRequestIdGenerator;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClient;
use Rebit\Bybit\Application\Advertisement\UseCase\OrderBookUseCase;

return [
    'services' => [
        'value' => [
            RebitHttpClient::class => [
                'className' => RebitHttpClient::class,
            ],
            RequestIdGeneratorInterface::class => [
                'className' => UuidRequestIdGenerator::class,
            ],
            ByBitConfig::class => [
                'className' => ByBitConfig::class,
                'constructorParams' => static function(): array {
                    // Ключи не должны храниться в коде. Значения берём из env/настроек.
                    // При отсутствии — безопасно подставляем пустые строки.
                    $apiKey = (string)(getenv('BYBIT_API_KEY') ?: '');
                    $apiSecret = (string)(getenv('BYBIT_API_SECRET') ?: '');
                    $baseUrl = (string)(getenv('BYBIT_BASE_URL') ?: 'https://api.bybit.com');
                    $recvWindow = (int)(getenv('BYBIT_RECV_WINDOW') ?: 5000);

                    return [
                        $apiKey,
                        $apiSecret,
                        $baseUrl,
                        $recvWindow,
                    ];
                },
            ],
            ByBitHttpClient::class => [
                'className' => ByBitHttpClient::class,
                'constructorParams' => static function(): array {
                    return [
                        ServiceLocator::getInstance()->get(ByBitConfig::class),
                        ServiceLocator::getInstance()->get(RebitHttpClient::class),
                        ServiceLocator::getInstance()->get(RequestIdGeneratorInterface::class),
                    ];
                },
            ],
            OrderBookUseCase::class => [
                'className' => OrderBookUseCase::class,
                'constructorParams' => static function(): array {
                    return [
                        ServiceLocator::getInstance()->get(ByBitHttpClient::class),
                    ];
                },
            ],
        ],
        'readonly' => true,
    ],
];
