<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmPaymentUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmReceiptUseCase;
use Rebit\Exchange\Application\Trade\UseCase\GetTradeUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ListTradesUseCase;
use Rebit\Exchange\Application\Trade\UseCase\SyncTradeHistoryUseCase;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Infrastructure\Bybit\BybitTradeGateway;
use Rebit\Exchange\Presentation\Command\SyncTradeHistoryCommand;
use Rebit\Exchange\Presentation\Command\SyncTradesCommand;
use Rebit\Exchange\Presentation\Controller\TradeController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

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
            ServiceLocator::getInstance()->get(NotificationPublisherInterface::class),
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
    SyncTradeHistoryCommand::class => [
        'className' => SyncTradeHistoryCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(SyncTradeHistoryUseCase::class),
            ServiceLocator::getInstance()->get(BybitConnectionResolverInterface::class),
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
