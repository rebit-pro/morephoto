<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\Currency\UseCase\GetCurrenciesUseCase;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrencyPairsUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;

final class CurrencyController extends BaseExchangeController
{
    public function __construct(
        private readonly GetCurrenciesUseCase $getCurrenciesUseCase,
        private readonly GetCurrencyPairsUseCase $getCurrencyPairsUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/exchange/currencies
     */
    public function currenciesAction(): ControllerJson
    {
        return $this->json(
            $this->getCurrenciesUseCase->execute(),
        );
    }

    /**
     * GET /api/v1/exchange/currency-pairs
     */
    public function pairsAction(): ControllerJson
    {
        return $this->json(
            $this->getCurrencyPairsUseCase->execute(),
        );
    }
}
