<?php

declare(strict_types=1);
use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Application\Contract\Wallet\BalanceQueryInterface;
use Rebit\Wallet\Application\Balance\Port\BybitBalanceGatewayInterface;
use Rebit\Wallet\Application\Balance\UseCase\GetBalancesUseCase;
use Rebit\Wallet\Application\Balance\UseCase\LockFundsUseCase;
use Rebit\Wallet\Application\Balance\UseCase\SyncBalancesUseCase;
use Rebit\Wallet\Application\Balance\UseCase\UnlockFundsUseCase;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Wallet\Infrastructure\Adapter\BalanceQueryAdapter;
use Rebit\Wallet\Infrastructure\Bybit\BybitBalanceGateway;
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
    GetBalancesUseCase::class => [
        'constructor' => static function(): GetBalancesUseCase {
            return new GetBalancesUseCase(
                ServiceLocator::getInstance()->get(BalanceRepository::class),
            );
        },
    ],
    LockFundsUseCase::class => [
        'constructor' => static function(): LockFundsUseCase {
            $sl = ServiceLocator::getInstance();

            return new LockFundsUseCase(
                $sl->get(BalanceRepository::class),
                $sl->get(TransactionRepository::class),
                $sl->get(BalanceCalculator::class),
            );
        },
    ],
    UnlockFundsUseCase::class => [
        'constructor' => static function(): UnlockFundsUseCase {
            $sl = ServiceLocator::getInstance();

            return new UnlockFundsUseCase(
                $sl->get(BalanceRepository::class),
                $sl->get(TransactionRepository::class),
                $sl->get(BalanceCalculator::class),
            );
        },
    ],
    BybitBalanceGatewayInterface::class => [
        'constructor' => static function(): BybitBalanceGatewayInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitBalanceGateway(
                $sl->get(BybitConnectionResolverInterface::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    SyncBalancesUseCase::class => [
        'constructor' => static function(): SyncBalancesUseCase {
            $sl = ServiceLocator::getInstance();

            return new SyncBalancesUseCase(
                $sl->get(BalanceRepository::class),
                $sl->get(BalanceCalculator::class),
                $sl->get(BybitBalanceGatewayInterface::class),
                $sl->get(CurrencyQueryInterface::class),
                Log::getLogger(LogChannelEnum::wallet),
            );
        },
    ],
    SyncBalancesCommand::class => [
        'constructor' => static function(): SyncBalancesCommand {
            $sl = ServiceLocator::getInstance();

            return new SyncBalancesCommand(
                $sl->get(SyncBalancesUseCase::class),
                $sl->get(BybitConnectionResolverInterface::class),
            );
        },
    ],
    BalanceQueryInterface::class => [
        'constructor' => static function(): BalanceQueryInterface {
            return new BalanceQueryAdapter(
                ServiceLocator::getInstance()->get(BalanceRepository::class),
            );
        },
    ],
    BalanceController::class => [
        'constructor' => static function(): BalanceController {
            $sl = ServiceLocator::getInstance();

            return new BalanceController(
                $sl->get(GetBalancesUseCase::class),
                $sl->get(SyncBalancesUseCase::class),
            );
        },
    ],
];
