<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO фильтрации для отчёта по оборотам денежных средств.
 */
final readonly class CashFlowFilterRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Date(message: 'dateFrom должен быть в формате Y-m-d.')]
        public ?string $dateFrom = null,
        #[Assert\Date(message: 'dateTo должен быть в формате Y-m-d.')]
        public ?string $dateTo = null,
        #[Assert\Positive(message: 'currencyId должен быть положительным числом.')]
        public ?int $currencyId = null,
    ) {}
}
