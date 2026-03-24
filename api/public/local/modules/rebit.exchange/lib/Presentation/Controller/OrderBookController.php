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
     * GET /api/v1/exchange/orderbook?token=USDT&fiat=RUB
     */
    public function listAction(string $token, string $fiat): ControllerJson
    {
        return $this->json(
            $this->getOrderBookUseCase->execute($token, $fiat),
        );
    }
}
