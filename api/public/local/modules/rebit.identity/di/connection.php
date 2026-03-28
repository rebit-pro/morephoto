<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Identity\Application\ApiConnection\Message\Handler\SyncIdentityMessageHandler;
use Rebit\Identity\Application\ApiConnection\UseCase\ConsumeIdentitySyncUseCase;
use Rebit\Identity\Infrastructure\Adapter\BybitConnectionResolver;
use Rebit\Identity\Application\ApiConnection\UseCase\ConnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\DisconnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\GetConnectionStatusUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\VerifyApiUseCase;
use Rebit\Identity\Infrastructure\ApiConnection\Messenger\IdentityMessengerFactory;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Rebit\Identity\Presentation\Command\ApiConnection\IdentitySyncConsumerCommand;
use Rebit\Identity\Presentation\Command\ApiConnection\TestIdentitySyncCommand;
use Rebit\Identity\Presentation\Controller\ApiConnectionController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Infrastructure\Messenger\AmqpConnectionFactory;
use Rebit\Share\Infrastructure\Messenger\ConsumerRunnerInterface;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

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
    SyncIdentityMessageHandler::class => [
        'className' => SyncIdentityMessageHandler::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(VerifyApiUseCase::class),
            Log::channel(LogChannelEnum::identity),
        ],
    ],
    'identity.sync.publisher' => [
        'constructor' => static fn(): MessagePublisherInterface => IdentityMessengerFactory::createPublisher(
            ServiceLocator::getInstance(),
        ),
    ],
    ConnectApiUseCase::class => [
        'className' => ConnectApiUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ApiConnectionRepository::class),
            ServiceLocator::getInstance()->get(ApiKeyEncryptor::class),
            ServiceLocator::getInstance()->get(ApiKeyMasker::class),
            ServiceLocator::getInstance()->get(BybitClientInterface::class),
        ],
    ],
    DisconnectApiUseCase::class => [
        'className' => DisconnectApiUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ApiConnectionRepository::class),
        ],
    ],
    VerifyApiUseCase::class => [
        'className' => VerifyApiUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ApiConnectionRepository::class),
            ServiceLocator::getInstance()->get(ApiKeyEncryptor::class),
            ServiceLocator::getInstance()->get(ApiKeyMasker::class),
            ServiceLocator::getInstance()->get(BybitClientInterface::class),
        ],
    ],
    GetConnectionStatusUseCase::class => [
        'className' => GetConnectionStatusUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ApiConnectionRepository::class),
            ServiceLocator::getInstance()->get(ApiKeyEncryptor::class),
            ServiceLocator::getInstance()->get(ApiKeyMasker::class),
        ],
    ],
    ConsumeIdentitySyncUseCase::class => [
        'className' => ConsumeIdentitySyncUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumerRunnerInterface::class),
            ServiceLocator::getInstance()->get(AmqpConnectionFactory::class),
            IdentityMessengerFactory::createBus(ServiceLocator::getInstance()),
        ],
    ],
    BybitConnectionResolverInterface::class => [
        'constructor' => static function(): BybitConnectionResolverInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitConnectionResolver(
                $sl->get(ApiConnectionRepository::class),
                $sl->get(ApiKeyEncryptor::class),
            );
        },
    ],
    IdentitySyncConsumerCommand::class => [
        'className' => IdentitySyncConsumerCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumeIdentitySyncUseCase::class),
        ],
    ],
    TestIdentitySyncCommand::class => [
        'className' => TestIdentitySyncCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get('identity.sync.publisher'),
        ],
    ],
    ApiConnectionController::class => [
        'className' => ApiConnectionController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConnectApiUseCase::class),
            ServiceLocator::getInstance()->get(DisconnectApiUseCase::class),
            ServiceLocator::getInstance()->get(VerifyApiUseCase::class),
            ServiceLocator::getInstance()->get(GetConnectionStatusUseCase::class),
        ],
    ],
];
