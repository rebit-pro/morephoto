<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\PaymentMethod\UseCase\GetPaymentMethodsUseCase;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;
use Rebit\Exchange\Presentation\Controller\PaymentMethodController;

return [
    PaymentMethodRepository::class => [
        'className' => PaymentMethodRepository::class,
    ],
    GetPaymentMethodsUseCase::class => [
        'constructor' => static function(): GetPaymentMethodsUseCase {
            return new GetPaymentMethodsUseCase(
                ServiceLocator::getInstance()->get(PaymentMethodRepository::class),
            );
        },
    ],
    PaymentMethodController::class => [
        'constructor' => static function(): PaymentMethodController {
            return new PaymentMethodController(
                ServiceLocator::getInstance()->get(GetPaymentMethodsUseCase::class),
            );
        },
    ],
];
