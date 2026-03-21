<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Auth\Application\Auth\Service\TokenResolver;
use Rebit\Auth\Application\Auth\UseCase\LoginUseCase;
use Rebit\Auth\Application\Auth\UseCase\LogoutUseCase;
use Rebit\Auth\Domain\User\Repository\UserRepository;
use Rebit\Auth\Domain\User\Service\TokenGenerator;
use Rebit\Auth\Presentation\Controller\AuthController;
use Rebit\Share\Application\Contract\Auth\TokenResolverInterface;

return [
    UserRepository::class => [
        'className' => UserRepository::class,
    ],
    TokenGenerator::class => [
        'className' => TokenGenerator::class,
    ],
    TokenResolverInterface::class => [
        'constructor' => static function (): TokenResolverInterface {
            return new TokenResolver(
                ServiceLocator::getInstance()->get(UserRepository::class),
            );
        },
    ],
    LoginUseCase::class => [
        'constructor' => static function (): LoginUseCase {
            $sl = ServiceLocator::getInstance();

            return new LoginUseCase(
                $sl->get(UserRepository::class),
                $sl->get(TokenGenerator::class),
                (int) (getenv('REBIT_TOKEN_TTL_HOURS') ?: 24),
            );
        },
    ],
    LogoutUseCase::class => [
        'constructor' => static function (): LogoutUseCase {
            return new LogoutUseCase(
                ServiceLocator::getInstance()->get(UserRepository::class),
            );
        },
    ],
    AuthController::class => [
        'constructor' => static function (): AuthController {
            $sl = ServiceLocator::getInstance();

            return new AuthController(
                $sl->get(LoginUseCase::class),
                $sl->get(LogoutUseCase::class),
                $sl->get(TokenResolverInterface::class),
            );
        },
    ],
];
