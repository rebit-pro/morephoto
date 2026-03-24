<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\PaymentMethod\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class PaymentMethodListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, PaymentMethodResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
