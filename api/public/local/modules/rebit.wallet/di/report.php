<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Wallet\Application\Transaction\UseCase\GetCashFlowReportUseCase;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Wallet\Presentation\Controller\ReportController;

return [
    GetCashFlowReportUseCase::class => [
        'className' => GetCashFlowReportUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TransactionRepository::class),
            ServiceLocator::getInstance()->get(CurrencyQueryInterface::class),
        ],
    ],
    ReportController::class => [
        'className' => ReportController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(GetCashFlowReportUseCase::class),
        ],
    ],
];
