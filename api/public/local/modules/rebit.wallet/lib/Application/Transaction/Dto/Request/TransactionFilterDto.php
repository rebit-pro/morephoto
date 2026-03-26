<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

/**
 * DTO фильтрации транзакций. Используется в контроллере через автомапинг.
 */
final readonly class TransactionFilterDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $type = null,
        public ?int $currencyId = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $limit = 50,
        public int $offset = 0,
    ) {}
}
