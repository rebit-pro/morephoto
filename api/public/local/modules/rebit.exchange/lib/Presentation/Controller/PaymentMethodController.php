<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\PaymentMethod\UseCase\GetPaymentMethodsUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Shared\Exception\RepositoryException;

final class PaymentMethodController extends BaseExchangeController
{
    public function __construct(
        private readonly GetPaymentMethodsUseCase $getPaymentMethodsUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/exchange/payment-methods
     * @throws RepositoryException
     */
    public function listAction(): ControllerJson
    {
        return $this->json(
            $this->getPaymentMethodsUseCase->execute(),
        );
    }
}
