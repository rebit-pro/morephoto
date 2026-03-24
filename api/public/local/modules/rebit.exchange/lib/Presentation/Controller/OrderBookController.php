<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\OrderBook\UseCase\GetOrderBookUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;

final class OrderBookController extends BaseExchangeController
{
    public function __construct(
        private readonly GetOrderBookUseCase $getOrderBookUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/exchange/order-book?currencyPairId=1&side=buy
     */
    public function listAction(int $currencyPairId, string $side): ControllerJson
    {
        return $this->json(
            $this->getOrderBookUseCase->execute($currencyPairId, $side),
        );
    }
}
