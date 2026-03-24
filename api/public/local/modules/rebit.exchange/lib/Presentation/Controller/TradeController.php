<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\Trade\UseCase\ConfirmPaymentUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ConfirmReceiptUseCase;
use Rebit\Exchange\Application\Trade\UseCase\GetTradeUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ListTradesUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;

final class TradeController extends BaseExchangeController
{
    public function __construct(
        private readonly ListTradesUseCase $listUseCase,
        private readonly GetTradeUseCase $getTradeUseCase,
        private readonly ConfirmPaymentUseCase $confirmPaymentUseCase,
        private readonly ConfirmReceiptUseCase $confirmReceiptUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/exchange/trades
     */
    public function listAction(?string $status = null): ControllerJson
    {
        return $this->json(
            $this->listUseCase->execute($this->getAuthUserId(), $status),
        );
    }

    /**
     * GET /api/v1/exchange/trades/{id}
     */
    public function detailAction(int $id): ControllerJson
    {
        return $this->json(
            $this->getTradeUseCase->execute($id, $this->getAuthUserId()),
        );
    }

    /**
     * POST /api/v1/exchange/trades/{id}/pay
     */
    public function payAction(int $id, string $paymentType, string $paymentId): ControllerJson
    {
        return $this->json(
            $this->confirmPaymentUseCase->execute($id, $this->getAuthUserId(), $paymentType, $paymentId),
        );
    }

    /**
     * POST /api/v1/exchange/trades/{id}/release
     */
    public function releaseAction(int $id): ControllerJson
    {
        return $this->json(
            $this->confirmReceiptUseCase->execute($id, $this->getAuthUserId()),
        );
    }
}
