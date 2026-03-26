<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Auth\Application\Auth\Contract\CaptchaVerifierInterface;
use Rebit\Auth\Application\Auth\Contract\LoginUserRepositoryInterface;
use Rebit\Auth\Application\Auth\Contract\RegistrationConfirmationMailerInterface;
use Rebit\Auth\Application\Auth\Contract\TokenGeneratorInterface;
use Rebit\Auth\Application\Auth\UseCase\ConfirmRegistrationUseCase;
use Rebit\Auth\Infrastructure\Adapter\BitrixMailEventRegistrationConfirmationMailer;
use Rebit\Auth\Infrastructure\Adapter\TokenResolver;
use Rebit\Auth\Infrastructure\Adapter\GeeTestCaptchaVerifier;
use Rebit\Auth\Application\Auth\UseCase\LoginUseCase;
use Rebit\Auth\Application\Auth\UseCase\LogoutUseCase;
use Rebit\Auth\Application\Auth\UseCase\RequestRegistrationCodeUseCase;
use Rebit\Auth\Domain\Registration\Repository\RegistrationConfirmationRepository;
use Rebit\Auth\Domain\Registration\Service\RegistrationCodeGenerator;
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
    RegistrationConfirmationRepository::class => [
        'className' => RegistrationConfirmationRepository::class,
    ],
    RegistrationCodeGenerator::class => [
        'className' => RegistrationCodeGenerator::class,
    ],
    TokenGeneratorInterface::class => [
        'constructor' => static function(): TokenGeneratorInterface {
            return ServiceLocator::getInstance()->get(TokenGenerator::class);
        },
    ],
    RegistrationConfirmationMailerInterface::class => [
        'constructor' => static function(): RegistrationConfirmationMailerInterface {
            return new BitrixMailEventRegistrationConfirmationMailer(
                siteId: (string)(getenv('REBIT_AUTH_MAIL_EVENT_SITE_ID') ?: 's1'),
            );
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
    RequestRegistrationCodeUseCase::class => [
        'constructor' => static function(): RequestRegistrationCodeUseCase {
            $sl = ServiceLocator::getInstance();

            return new RequestRegistrationCodeUseCase(
                userRepository: $sl->get(UserRepository::class),
                registrationConfirmationRepository: $sl->get(RegistrationConfirmationRepository::class),
                registrationCodeGenerator: $sl->get(RegistrationCodeGenerator::class),
                registrationConfirmationMailer: $sl->get(RegistrationConfirmationMailerInterface::class),
                codeTtlMinutes: (int)(getenv('REBIT_AUTH_REGISTRATION_CODE_TTL_MINUTES') ?: 15),
                resendCooldownSeconds: (int)(getenv('REBIT_AUTH_REGISTRATION_RESEND_COOLDOWN_SECONDS') ?: 60),
            );
        },
    ],
    ConfirmRegistrationUseCase::class => [
        'constructor' => static function(): ConfirmRegistrationUseCase {
            $sl = ServiceLocator::getInstance();

            return new ConfirmRegistrationUseCase(
                userRepository: $sl->get(UserRepository::class),
                registrationConfirmationRepository: $sl->get(RegistrationConfirmationRepository::class),
                tokenGenerator: $sl->get(TokenGeneratorInterface::class),
                tokenTtlHours: (int)(getenv('REBIT_TOKEN_TTL_HOURS') ?: 24),
                maxAttempts: (int)(getenv('REBIT_AUTH_REGISTRATION_MAX_ATTEMPTS') ?: 5),
            );
        },
    ],
    AuthController::class => [
        'constructor' => static function(): AuthController {
            $sl = ServiceLocator::getInstance();

            return new AuthController(
                loginUseCase: $sl->get(LoginUseCase::class),
                logoutUseCase: $sl->get(LogoutUseCase::class),
                requestRegistrationCodeUseCase: $sl->get(RequestRegistrationCodeUseCase::class),
                confirmRegistrationUseCase: $sl->get(ConfirmRegistrationUseCase::class),
                tokenResolver: $sl->get(TokenResolverInterface::class),
            );
        },
    ],
];
