<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\OrderBook\Port\BybitOrderBookGatewayInterface;
use Rebit\Exchange\Application\OrderBook\UseCase\CleanStaleOrdersUseCase;
use Rebit\Exchange\Application\OrderBook\UseCase\GetOrderBookUseCase;
use Rebit\Exchange\Application\OrderBook\UseCase\SyncOrderBookUseCase;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
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
        'constructor' => static function(): GetOrderBookUseCase {
            $sl = ServiceLocator::getInstance();
            return new GetOrderBookUseCase(
                $sl->get(OrderBookRepository::class),
                $sl->get(CurrencyPairRepository::class),
            );
        },
    ],
    SyncOrderBookUseCase::class => [
        'constructor' => static function(): SyncOrderBookUseCase {
            $sl = ServiceLocator::getInstance();

            return new SyncOrderBookUseCase(
                $sl->get(OrderBookRepository::class),
                $sl->get(CurrencyPairRepository::class),
                $sl->get(BybitOrderBookGatewayInterface::class),
                Log::getLogger(LogChannelEnum::exchange),
            );
        },
    ],
    SyncOrderBookCommand::class => [
        'constructor' => static function(): SyncOrderBookCommand {
            $sl = ServiceLocator::getInstance();

            return new SyncOrderBookCommand(
                $sl->get(SyncOrderBookUseCase::class),
                $sl->get(BybitConnectionResolverInterface::class),
            );
        },
    ],
    CleanStaleOrdersUseCase::class => [
        'constructor' => static function(): CleanStaleOrdersUseCase {
            return new CleanStaleOrdersUseCase(
                ServiceLocator::getInstance()->get(OrderBookRepository::class),
                Log::getLogger(LogChannelEnum::exchange),
            );
        },
    ],
    CleanStaleOrdersCommand::class => [
        'constructor' => static function(): CleanStaleOrdersCommand {
            return new CleanStaleOrdersCommand(
                ServiceLocator::getInstance()->get(CleanStaleOrdersUseCase::class),
            );
        },
    ],
    OrderBookController::class => [
        'constructor' => static function(): OrderBookController {
            return new OrderBookController(
                ServiceLocator::getInstance()->get(GetOrderBookUseCase::class),
            );
        },
    ],
];
