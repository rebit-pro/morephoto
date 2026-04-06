<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Application\Contract\Wallet\BalanceSyncPublisherInterface;
use Rebit\Share\Application\Contract\Wallet\BalanceQueryInterface;
use Rebit\Share\Infrastructure\Messenger\AmqpConnectionFactory;
use Rebit\Share\Infrastructure\Messenger\ConsumerRunnerInterface;
use Rebit\Wallet\Application\Balance\Port\BybitBalanceGatewayInterface;
use Rebit\Wallet\Application\Balance\Message\Handler\SyncBalanceMessageHandler;
use Rebit\Wallet\Application\Balance\UseCase\ConsumeBalanceSyncUseCase;
use Rebit\Wallet\Application\Balance\UseCase\GetBalancesUseCase;
use Rebit\Wallet\Application\Balance\UseCase\LockFundsUseCase;
use Rebit\Wallet\Application\Balance\UseCase\SyncBalancesUseCase;
use Rebit\Wallet\Application\Balance\UseCase\UnlockFundsUseCase;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Wallet\Infrastructure\Adapter\BalanceQueryAdapter;
use Rebit\Wallet\Infrastructure\Balance\Messenger\BalanceSyncPublisher;
use Rebit\Wallet\Infrastructure\Balance\Messenger\WalletMessengerFactory;
use Rebit\Wallet\Infrastructure\Bybit\BybitBalanceGateway;
use Rebit\Wallet\Presentation\Command\Balance\BalanceSyncConsumerCommand;
use Rebit\Wallet\Presentation\Command\Balance\TestBalanceSyncCommand;
use Rebit\Wallet\Presentation\Command\SyncBalancesCommand;
use Rebit\Wallet\Presentation\Controller\BalanceController;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    BalanceCalculator::class => [
        'className' => BalanceCalculator::class,
    ],
    BalanceRepository::class => [
        'className' => BalanceRepository::class,
    ],
    SyncBalanceMessageHandler::class => [
        'className' => SyncBalanceMessageHandler::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(SyncBalancesUseCase::class),
            Log::channel(LogChannelEnum::wallet),
        ],
    ],
    BalanceSyncPublisherInterface::class => [
        'constructor' => static fn(): BalanceSyncPublisherInterface => new BalanceSyncPublisher(
            WalletMessengerFactory::createPublisher(ServiceLocator::getInstance()),
        ),
    ],
    GetBalancesUseCase::class => [
        'className' => GetBalancesUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(BalanceRepository::class),
            ServiceLocator::getInstance()->get(CurrencyQueryInterface::class),
            ServiceLocator::getInstance()->get(CurrencyRubRateQueryInterface::class),
        ],
    ],
    LockFundsUseCase::class => [
        'className' => LockFundsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(BalanceRepository::class),
            ServiceLocator::getInstance()->get(TransactionRepository::class),
            ServiceLocator::getInstance()->get(BalanceCalculator::class),
        ],
    ],
    UnlockFundsUseCase::class => [
        'className' => UnlockFundsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(BalanceRepository::class),
            ServiceLocator::getInstance()->get(TransactionRepository::class),
            ServiceLocator::getInstance()->get(BalanceCalculator::class),
        ],
    ],
    BybitBalanceGatewayInterface::class => [
        'constructor' => static function(): BybitBalanceGatewayInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitBalanceGateway(
                $sl->get(BybitConnectionResolverInterface::class),
                $sl->get(BybitClientInterface::class),
                Log::channel(LogChannelEnum::bybit),
            );
        },
    ],
    SyncBalancesUseCase::class => [
        'className' => SyncBalancesUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(BalanceRepository::class),
            ServiceLocator::getInstance()->get(BalanceCalculator::class),
            ServiceLocator::getInstance()->get(BybitBalanceGatewayInterface::class),
            ServiceLocator::getInstance()->get(CurrencyQueryInterface::class),
            ServiceLocator::getInstance()->get(GetBalancesUseCase::class),
            Log::getLogger(LogChannelEnum::wallet),
        ],
    ],
    SyncBalancesCommand::class => [
        'className' => SyncBalancesCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(SyncBalancesUseCase::class),
            ServiceLocator::getInstance()->get(BybitConnectionResolverInterface::class),
        ],
    ],
    ConsumeBalanceSyncUseCase::class => [
        'className' => ConsumeBalanceSyncUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumerRunnerInterface::class),
            ServiceLocator::getInstance()->get(AmqpConnectionFactory::class),
            WalletMessengerFactory::createBus(ServiceLocator::getInstance()),
        ],
    ],
    BalanceSyncConsumerCommand::class => [
        'className' => BalanceSyncConsumerCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumeBalanceSyncUseCase::class),
        ],
    ],
    TestBalanceSyncCommand::class => [
        'className' => TestBalanceSyncCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(BalanceSyncPublisherInterface::class),
        ],
    ],
    BalanceQueryInterface::class => [
        'constructor' => static function(): BalanceQueryInterface {
            return new BalanceQueryAdapter(
                ServiceLocator::getInstance()->get(BalanceRepository::class),
            );
        },
    ],
    BalanceController::class => [
        'className' => BalanceController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(GetBalancesUseCase::class),
            ServiceLocator::getInstance()->get(SyncBalancesUseCase::class),
        ],
    ],
];
