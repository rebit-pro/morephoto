<?php

declare(strict_types=1);

namespace Rebit\Wallet\Presentation\Controller;

use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Wallet\Application\Transaction\UseCase\ExportTransactionsUseCase;
use Rebit\Wallet\Application\Transaction\UseCase\ListTransactionsUseCase;
use Rebit\Wallet\Domain\Transaction\Dto\Request\TransactionFilterDto;
use Rebit\Wallet\Infrastructure\Controller\BaseWalletController;

final class TransactionController extends BaseWalletController
{
    public function __construct(
        private readonly ListTransactionsUseCase $listTransactionsUseCase,
        private readonly ExportTransactionsUseCase $exportTransactionsUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/wallet/transactions
     */
    public function listAction(TransactionFilterDto $filter): ControllerJson
    {
        return $this->json(
            $this->listTransactionsUseCase->execute($this->getAuthUserId(), $filter),
        );
    }

    /**
     * GET /api/v1/wallet/transactions/export
     *
     * @todo Реализовать отдачу файла CSV/Excel вместо JSON.
     */
    public function exportAction(TransactionFilterDto $filter): ControllerJson
    {
        return $this->json(
            $this->exportTransactionsUseCase->execute($this->getAuthUserId(), $filter),
        );
    }
}
