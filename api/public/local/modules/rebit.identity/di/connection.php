<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Identity\Application\ApiConnection\UseCase\ConnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\DisconnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\GetConnectionStatusUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\VerifyApiUseCase;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Rebit\Identity\Presentation\Controller\ApiConnectionController;

return [
    ApiKeyEncryptor::class => [
        'constructor' => static function(): ApiKeyEncryptor {
            $key = getenv('REBIT_ENCRYPTION_KEY');

            if (false === $key || '' === $key) {
                throw new RuntimeException('REBIT_ENCRYPTION_KEY is not set');
            }

            return new ApiKeyEncryptor($key);
        },
    ],
    ApiKeyMasker::class => [
        'className' => ApiKeyMasker::class,
    ],
    ApiConnectionRepository::class => [
        'className' => ApiConnectionRepository::class,
    ],
    ConnectApiUseCase::class => [
        'constructor' => static function(): ConnectApiUseCase {
            $sl = ServiceLocator::getInstance();

            return new ConnectApiUseCase(
                $sl->get(ApiConnectionRepository::class),
                $sl->get(ApiKeyEncryptor::class),
                $sl->get(ApiKeyMasker::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    DisconnectApiUseCase::class => [
        'constructor' => static function(): DisconnectApiUseCase {
            return new DisconnectApiUseCase(
                ServiceLocator::getInstance()->get(ApiConnectionRepository::class),
            );
        },
    ],
    VerifyApiUseCase::class => [
        'constructor' => static function(): VerifyApiUseCase {
            $sl = ServiceLocator::getInstance();

            return new VerifyApiUseCase(
                $sl->get(ApiConnectionRepository::class),
                $sl->get(ApiKeyEncryptor::class),
                $sl->get(ApiKeyMasker::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    GetConnectionStatusUseCase::class => [
        'constructor' => static function(): GetConnectionStatusUseCase {
            $sl = ServiceLocator::getInstance();

            return new GetConnectionStatusUseCase(
                $sl->get(ApiConnectionRepository::class),
                $sl->get(ApiKeyEncryptor::class),
                $sl->get(ApiKeyMasker::class),
            );
        },
    ],
    ApiConnectionController::class => [
        'constructor' => static function(): ApiConnectionController {
            $sl = ServiceLocator::getInstance();

            return new ApiConnectionController(
                $sl->get(ConnectApiUseCase::class),
                $sl->get(DisconnectApiUseCase::class),
                $sl->get(VerifyApiUseCase::class),
                $sl->get(GetConnectionStatusUseCase::class),
            );
        },
    ],
];
