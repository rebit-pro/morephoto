<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\OrderBook\Port\BybitOrderBookGatewayInterface;
use Rebit\Exchange\Application\OrderBook\UseCase\CleanStaleOrdersUseCase;
use Rebit\Exchange\Application\OrderBook\UseCase\GetOrderBookUseCase;
use Rebit\Exchange\Application\OrderBook\UseCase\SyncOrderBookUseCase;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;
use Rebit\Exchange\Infrastructure\Bybit\BybitOrderBookGateway;
use Rebit\Exchange\Presentation\Command\CleanStaleOrdersCommand;
use Rebit\Exchange\Presentation\Command\SyncOrderBookCommand;
use Rebit\Exchange\Presentation\Controller\OrderBookController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    OrderBookRepository::class => [
        'className' => OrderBookRepository::class,
    ],
    BybitOrderBookGatewayInterface::class => [
        'constructor' => static function(): BybitOrderBookGatewayInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitOrderBookGateway(
                $sl->get(BybitConnectionResolverInterface::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    GetOrderBookUseCase::class => [
        'className' => GetOrderBookUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(OrderBookRepository::class),
            ServiceLocator::getInstance()->get(CurrencyPairRepository::class),
            ServiceLocator::getInstance()->get(PaymentMethodRepository::class),
        ],
    ],
    SyncOrderBookUseCase::class => [
        'className' => SyncOrderBookUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(OrderBookRepository::class),
            ServiceLocator::getInstance()->get(CurrencyPairRepository::class),
            ServiceLocator::getInstance()->get(BybitOrderBookGatewayInterface::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    SyncOrderBookCommand::class => [
        'className' => SyncOrderBookCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(SyncOrderBookUseCase::class),
            ServiceLocator::getInstance()->get(BybitConnectionResolverInterface::class),
        ],
    ],
    CleanStaleOrdersUseCase::class => [
        'className' => CleanStaleOrdersUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(OrderBookRepository::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    CleanStaleOrdersCommand::class => [
        'className' => CleanStaleOrdersCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(CleanStaleOrdersUseCase::class),
        ],
    ],
    OrderBookController::class => [
        'className' => OrderBookController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(GetOrderBookUseCase::class),
        ],
    ],
];
