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
        'className' => ListTransactionsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TransactionRepository::class),
            ServiceLocator::getInstance()->get(CurrencyQueryInterface::class),
        ],
    ],
    ExportTransactionsUseCase::class => [
        'className' => ExportTransactionsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ListTransactionsUseCase::class),
        ],
    ],
    TransactionController::class => [
        'className' => TransactionController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ListTransactionsUseCase::class),
            ServiceLocator::getInstance()->get(ExportTransactionsUseCase::class),
        ],
    ],
];
