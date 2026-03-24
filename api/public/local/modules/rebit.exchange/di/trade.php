<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmPaymentUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmReceiptUseCase;
use Rebit\Exchange\Application\Trade\UseCase\GetTradeUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ListTradesUseCase;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Infrastructure\Bybit\BybitTradeGateway;
use Rebit\Exchange\Presentation\Command\SyncTradesCommand;
use Rebit\Exchange\Presentation\Controller\TradeController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;

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
        'constructor' => static function(): ListTradesUseCase {
            return new ListTradesUseCase(
                ServiceLocator::getInstance()->get(TradeRepository::class),
            );
        },
    ],
    GetTradeUseCase::class => [
        'constructor' => static function(): GetTradeUseCase {
            $sl = ServiceLocator::getInstance();

            return new GetTradeUseCase(
                $sl->get(TradeRepository::class),
                $sl->get(BybitTradeGatewayInterface::class),
            );
        },
    ],
    ConfirmPaymentUseCase::class => [
        'constructor' => static function(): ConfirmPaymentUseCase {
            $sl = ServiceLocator::getInstance();

            return new ConfirmPaymentUseCase(
                $sl->get(TradeRepository::class),
                $sl->get(BybitTradeGatewayInterface::class),
            );
        },
    ],
    ConfirmReceiptUseCase::class => [
        'constructor' => static function(): ConfirmReceiptUseCase {
            $sl = ServiceLocator::getInstance();

            return new ConfirmReceiptUseCase(
                $sl->get(TradeRepository::class),
                $sl->get(BybitTradeGatewayInterface::class),
            );
        },
    ],
    SyncTradesCommand::class => [
        'constructor' => static function(): SyncTradesCommand {
            $sl = ServiceLocator::getInstance();

            return new SyncTradesCommand(
                $sl->get(TradeRepository::class),
                $sl->get(BybitTradeGatewayInterface::class),
                $sl->get(BybitConnectionResolverInterface::class),
            );
        },
    ],
    TradeController::class => [
        'constructor' => static function(): TradeController {
            $sl = ServiceLocator::getInstance();

            return new TradeController(
                $sl->get(ListTradesUseCase::class),
                $sl->get(GetTradeUseCase::class),
                $sl->get(ConfirmPaymentUseCase::class),
                $sl->get(ConfirmReceiptUseCase::class),
            );
        },
    ],
];
