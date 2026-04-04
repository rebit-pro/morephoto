<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Trade\Message\Handler\TradeDiscoveredMessageHandler;
use Rebit\Exchange\Application\Trade\Message\Handler\TradeStatusChangedMessageHandler;
use Rebit\Exchange\Application\Trade\Port\BybitCounterpartyGatewayInterface;
use Rebit\Exchange\Application\Trade\UseCase\ConsumeTradeEventsUseCase;
use Rebit\Exchange\Application\Trade\UseCase\EnrichTradeFromBybitUseCase;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Application\Trade\Port\TradeEventPublisherInterface;
use Rebit\Exchange\Application\Trade\UseCase\SyncCounterpartyUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmPaymentUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmReceiptUseCase;
use Rebit\Exchange\Application\Trade\UseCase\GetTradeUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ListTradesUseCase;
use Rebit\Exchange\Application\Trade\UseCase\SyncTradeHistoryUseCase;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Infrastructure\Bybit\BybitTradeGateway;
use Rebit\Exchange\Infrastructure\Trade\Messenger\ExchangeTradeMessengerFactory;
use Rebit\Exchange\Infrastructure\Trade\Messenger\TradeEventPublisher;
use Rebit\Exchange\Presentation\Command\SyncTradeHistoryCommand;
use Rebit\Exchange\Presentation\Command\SyncTradesCommand;
use Rebit\Exchange\Presentation\Command\Trade\TestTradeEventCommand;
use Rebit\Exchange\Presentation\Command\Trade\TradeEventConsumerCommand;
use Rebit\Exchange\Application\TradeChat\UseCase\StartTradeChatScriptUseCase;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\Counterparty\Repository\CounterpartyRepository;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;
use Rebit\Exchange\Presentation\Controller\TradeController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;
use Rebit\Share\Application\Contract\Wallet\BalanceSyncPublisherInterface;
use Rebit\Share\Infrastructure\Messenger\AmqpConnectionFactory;
use Rebit\Share\Infrastructure\Messenger\ConsumerRunnerInterface;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;
use Rebit\Exchange\Infrastructure\Bybit\BybitCounterpartyGateway;

return [
    TradeRepository::class => [
        'className' => TradeRepository::class,
    ],
    BybitTradeGatewayInterface::class => [
        'constructor' => static function(): BybitTradeGatewayInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitTradeGateway(
                $sl->get(BybitConnectionResolverInterface::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    BybitCounterpartyGatewayInterface::class => [
        'constructor' => static function(): BybitCounterpartyGatewayInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitCounterpartyGateway(
                $sl->get(BybitConnectionResolverInterface::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    CounterpartyRepository::class => [
        'className' => CounterpartyRepository::class,
    ],
    TradeDiscoveredMessageHandler::class => [
        'className' => TradeDiscoveredMessageHandler::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(EnrichTradeFromBybitUseCase::class),
            ServiceLocator::getInstance()->get(SyncCounterpartyUseCase::class),
            ServiceLocator::getInstance()->get(NotificationPublisherInterface::class),
            ServiceLocator::getInstance()->get(StartTradeChatScriptUseCase::class),
            Log::channel(LogChannelEnum::exchange),
        ],
    ],
    TradeStatusChangedMessageHandler::class => [
        'className' => TradeStatusChangedMessageHandler::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptExecutionRepository::class),
            ServiceLocator::getInstance()->get(BalanceSyncPublisherInterface::class),
            ServiceLocator::getInstance()->get(NotificationPublisherInterface::class),
            Log::channel(LogChannelEnum::exchange),
        ],
    ],
    TradeEventPublisherInterface::class => [
        'constructor' => static fn(): TradeEventPublisherInterface => new TradeEventPublisher(
            ExchangeTradeMessengerFactory::createPublisher(ServiceLocator::getInstance()),
        ),
    ],
    EnrichTradeFromBybitUseCase::class => [
        'className' => EnrichTradeFromBybitUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(BybitTradeGatewayInterface::class),
            ServiceLocator::getInstance()->get(AdvertisementRepository::class),
            ServiceLocator::getInstance()->get(PaymentMethodRepository::class),
            ServiceLocator::getInstance()->get(TradeRepository::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    SyncCounterpartyUseCase::class => [
        'className' => SyncCounterpartyUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(BybitCounterpartyGatewayInterface::class),
            ServiceLocator::getInstance()->get(CounterpartyRepository::class),
            ServiceLocator::getInstance()->get(TradeRepository::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    ListTradesUseCase::class => [
        'className' => ListTradesUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
        ],
    ],
    GetTradeUseCase::class => [
        'className' => GetTradeUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(BybitTradeGatewayInterface::class),
        ],
    ],
    ConfirmPaymentUseCase::class => [
        'className' => ConfirmPaymentUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(BybitTradeGatewayInterface::class),
        ],
    ],
    ConfirmReceiptUseCase::class => [
        'className' => ConfirmReceiptUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(BybitTradeGatewayInterface::class),
        ],
    ],
    SyncTradesCommand::class => [
        'className' => SyncTradesCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(BybitTradeGatewayInterface::class),
            ServiceLocator::getInstance()->get(BybitConnectionResolverInterface::class),
            ServiceLocator::getInstance()->get(TradeEventPublisherInterface::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    SyncTradeHistoryUseCase::class => [
        'className' => SyncTradeHistoryUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(BybitTradeGatewayInterface::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    ConsumeTradeEventsUseCase::class => [
        'className' => ConsumeTradeEventsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumerRunnerInterface::class),
            ServiceLocator::getInstance()->get(AmqpConnectionFactory::class),
            ExchangeTradeMessengerFactory::createBus(ServiceLocator::getInstance()),
        ],
    ],
    SyncTradeHistoryCommand::class => [
        'className' => SyncTradeHistoryCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(SyncTradeHistoryUseCase::class),
            ServiceLocator::getInstance()->get(BybitConnectionResolverInterface::class),
        ],
    ],
    TradeEventConsumerCommand::class => [
        'className' => TradeEventConsumerCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumeTradeEventsUseCase::class),
        ],
    ],
    TestTradeEventCommand::class => [
        'className' => TestTradeEventCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeEventPublisherInterface::class),
        ],
    ],
    TradeController::class => [
        'className' => TradeController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ListTradesUseCase::class),
            ServiceLocator::getInstance()->get(GetTradeUseCase::class),
            ServiceLocator::getInstance()->get(ConfirmPaymentUseCase::class),
            ServiceLocator::getInstance()->get(ConfirmReceiptUseCase::class),
        ],
    ],
];
