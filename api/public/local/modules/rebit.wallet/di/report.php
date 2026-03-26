<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Wallet\Application\Transaction\UseCase\GetCashFlowReportUseCase;
use Rebit\Wallet\Domain\Transaction\Repository\TransactionRepository;
use Rebit\Wallet\Presentation\Controller\ReportController;

return [
    GetCashFlowReportUseCase::class => [
        'constructor' => static function(): GetCashFlowReportUseCase {
            $sl = ServiceLocator::getInstance();

            return new GetCashFlowReportUseCase(
                $sl->get(TransactionRepository::class),
                $sl->get(CurrencyQueryInterface::class),
            );
        },
    ],
    ReportController::class => [
        'constructor' => static function(): ReportController {
            return new ReportController(
                ServiceLocator::getInstance()->get(GetCashFlowReportUseCase::class),
            );
        },
    ],
];
