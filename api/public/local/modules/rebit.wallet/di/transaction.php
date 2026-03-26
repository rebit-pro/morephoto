<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Wallet\Application\Transaction\UseCase\ExportTransactionsUseCase;
use Rebit\Wallet\Application\Transaction\UseCase\ListTransactionsUseCase;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Wallet\Presentation\Controller\TransactionController;

return [
    TransactionRepository::class => [
        'className' => TransactionRepository::class,
    ],
    ListTransactionsUseCase::class => [
        'constructor' => static function(): ListTransactionsUseCase {
            $sl = ServiceLocator::getInstance();

            return new ListTransactionsUseCase(
                $sl->get(TransactionRepository::class),
                $sl->get(CurrencyQueryInterface::class),
            );
        },
    ],
    ExportTransactionsUseCase::class => [
        'constructor' => static function(): ExportTransactionsUseCase {
            return new ExportTransactionsUseCase(
                ServiceLocator::getInstance()->get(ListTransactionsUseCase::class),
            );
        },
    ],
    TransactionController::class => [
        'constructor' => static function(): TransactionController {
            $sl = ServiceLocator::getInstance();

            return new TransactionController(
                $sl->get(ListTransactionsUseCase::class),
                $sl->get(ExportTransactionsUseCase::class),
            );
        },
    ],
];
