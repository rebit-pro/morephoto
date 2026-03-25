<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Auth\Application\Auth\Contract\CaptchaVerifierInterface;
use Rebit\Auth\Application\Auth\Contract\LoginUserRepositoryInterface;
use Rebit\Auth\Application\Auth\Contract\TokenGeneratorInterface;
use Rebit\Auth\Infrastructure\Adapter\TokenResolver;
use Rebit\Auth\Infrastructure\Adapter\GeeTestCaptchaVerifier;
use Rebit\Auth\Application\Auth\UseCase\LoginUseCase;
use Rebit\Auth\Application\Auth\UseCase\LogoutUseCase;
use Rebit\Auth\Domain\User\Repository\UserRepository;
use Rebit\Auth\Domain\User\Service\TokenGenerator;
use Rebit\Auth\Presentation\Controller\AuthController;
use Rebit\Share\Application\Contract\Auth\TokenResolverInterface;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClientFactory;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    UserRepository::class => [
        'className' => UserRepository::class,
    ],
    LoginUserRepositoryInterface::class => [
        'constructor' => static function(): LoginUserRepositoryInterface {
            return ServiceLocator::getInstance()->get(UserRepository::class);
        },
    ],
    TokenGenerator::class => [
        'className' => TokenGenerator::class,
    ],
    TokenGeneratorInterface::class => [
        'constructor' => static function(): TokenGeneratorInterface {
            return ServiceLocator::getInstance()->get(TokenGenerator::class);
        },
    ],
    CaptchaVerifierInterface::class => [
        'constructor' => static function(): CaptchaVerifierInterface {
            $logger = Log::channel(LogChannelEnum::auth);

            return new GeeTestCaptchaVerifier(
                RebitHttpClientFactory::create($logger),
                $logger,
                (string)(getenv('REBIT_GEETEST_CAPTCHA_ID') ?: ''),
                (string)(getenv('REBIT_GEETEST_CAPTCHA_KEY') ?: ''),
                filter_var(getenv('REBIT_GEETEST_ENABLED') ?: '0', FILTER_VALIDATE_BOOL),
                filter_var(getenv('REBIT_GEETEST_BYPASS') ?: '0', FILTER_VALIDATE_BOOL),
                (string)(getenv('REBIT_GEETEST_API_URL') ?: 'https://gcaptcha4.geetest.com'),
            );
        },
    ],
    TokenResolverInterface::class => [
        'constructor' => static function(): TokenResolverInterface {
            return new TokenResolver(
                ServiceLocator::getInstance()->get(UserRepository::class),
            );
        },
    ],
    LoginUseCase::class => [
        'constructor' => static function(): LoginUseCase {
            $sl = ServiceLocator::getInstance();

            return new LoginUseCase(
                $sl->get(LoginUserRepositoryInterface::class),
                $sl->get(TokenGeneratorInterface::class),
                $sl->get(CaptchaVerifierInterface::class),
                (int)(getenv('REBIT_TOKEN_TTL_HOURS') ?: 24),
            );
        },
    ],
    LogoutUseCase::class => [
        'constructor' => static function(): LogoutUseCase {
            return new LogoutUseCase(
                ServiceLocator::getInstance()->get(UserRepository::class),
            );
        },
    ],
    AuthController::class => [
        'constructor' => static function(): AuthController {
            $sl = ServiceLocator::getInstance();

            return new AuthController(
                $sl->get(LoginUseCase::class),
                $sl->get(LogoutUseCase::class),
                $sl->get(TokenResolverInterface::class),
            );
        },
    ],
];
