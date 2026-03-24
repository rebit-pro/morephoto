<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\PaymentMethod\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class PaymentMethodResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public int $sort,
    ) {}
}
