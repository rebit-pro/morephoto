<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO для подтверждения оплаты покупателем.
 */
final readonly class ConfirmPaymentRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'paymentType обязателен.')]
        public string $paymentType,
        #[Assert\NotBlank(message: 'paymentId обязателен.')]
        public string $paymentId,
    ) {}
}
