<?php

declare(strict_types=1);

namespace Rebit\Wallet\Presentation\Controller;

use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Transaction\Dto\Request\CashFlowFilterRequestDto;
use Rebit\Wallet\Application\Transaction\UseCase\GetCashFlowReportUseCase;
use Rebit\Wallet\Infrastructure\Controller\BaseWalletController;

final class ReportController extends BaseWalletController
{
    public function __construct(
        private readonly GetCashFlowReportUseCase $getCashFlowReportUseCase,
    ) {}

    /**
     * GET /api/v1/wallet/reports/cash-flow
     *
     * @throws RepositoryException
     */
    public function cashFlowAction(CashFlowFilterRequestDto $filter): ControllerJson
    {
        return $this->json(
            $this->getCashFlowReportUseCase->execute($this->getAuthUserId(), $filter),
        );
    }
}
