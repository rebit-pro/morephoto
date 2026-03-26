<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

/**
 * DTO для подтверждения оплаты покупателем.
 */
final readonly class ConfirmPaymentRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $paymentType,
        public string $paymentId,
    ) {}
}
