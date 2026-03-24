<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\PaymentMethod\UseCase;

use Rebit\Exchange\Application\PaymentMethod\Dto\Result\PaymentMethodListResultDto;
use Rebit\Exchange\Application\PaymentMethod\Dto\Result\PaymentMethodResultDto;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class GetPaymentMethodsUseCase
{
    public function __construct(
        private PaymentMethodRepository $paymentMethodRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(): PaymentMethodListResultDto
    {
        $methods = $this->paymentMethodRepository->findActive();

        $items = [];
        foreach ($methods as $method) {
            $items[] = new PaymentMethodResultDto(
                id: $method->getId(),
                code: $method->getUfCode(),
                name: $method->getUfName(),
                sort: $method->getUfSort(),
            );
        }

        return new PaymentMethodListResultDto($items);
    }
}
