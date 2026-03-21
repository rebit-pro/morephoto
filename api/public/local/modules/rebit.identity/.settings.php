<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Identity\Application\ApiConnection\UseCase\ConnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\DisconnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\GetConnectionStatusUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\VerifyApiUseCase;
use Rebit\Identity\Controller\ApiConnectionController;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;

return [
    'services' => [
        'value' => [
            ApiKeyEncryptor::class => [
                'className' => ApiKeyEncryptor::class,
                'constructorParams' => static function () {
                    $key = getenv('REBIT_ENCRYPTION_KEY');

                    if (false === $key || '' === $key) {
                        throw new RuntimeException('REBIT_ENCRYPTION_KEY is not set');
                    }

                    return [$key];
                },
            ],
            ApiConnectionRepository::class => [
                'className' => ApiConnectionRepository::class,
            ],
            ConnectApiUseCase::class => [
                'className' => ConnectApiUseCase::class,
                'constructorParams' => static function () {
                    $sl = ServiceLocator::getInstance();

                    return [
                        $sl->get(ApiConnectionRepository::class),
                        $sl->get(ApiKeyEncryptor::class),
                        $sl->get(BybitClientInterface::class),
                    ];
                },
            ],
            DisconnectApiUseCase::class => [
                'className' => DisconnectApiUseCase::class,
                'constructorParams' => static function () {
                    return [
                        ServiceLocator::getInstance()->get(ApiConnectionRepository::class),
                    ];
                },
            ],
            VerifyApiUseCase::class => [
                'className' => VerifyApiUseCase::class,
                'constructorParams' => static function () {
                    $sl = ServiceLocator::getInstance();

                    return [
                        $sl->get(ApiConnectionRepository::class),
                        $sl->get(ApiKeyEncryptor::class),
                        $sl->get(BybitClientInterface::class),
                    ];
                },
            ],
            GetConnectionStatusUseCase::class => [
                'className' => GetConnectionStatusUseCase::class,
                'constructorParams' => static function () {
                    $sl = ServiceLocator::getInstance();

                    return [
                        $sl->get(ApiConnectionRepository::class),
                        $sl->get(ApiKeyEncryptor::class),
                    ];
                },
            ],
            ApiConnectionController::class => [
                'className' => ApiConnectionController::class,
                'constructorParams' => static function () {
                    $sl = ServiceLocator::getInstance();

                    return [
                        $sl->get(ConnectApiUseCase::class),
                        $sl->get(DisconnectApiUseCase::class),
                        $sl->get(VerifyApiUseCase::class),
                        $sl->get(GetConnectionStatusUseCase::class),
                    ];
                },
            ],
        ],
        'readonly' => true,
    ],
];
