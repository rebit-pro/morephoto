<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

/**
 * DTO фильтрации транзакций. Используется в контроллере через автомапинг.
 */
final class TransactionFilterDto implements RequestDtoInterface
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?int $currencyId = null,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
        public readonly int $limit = 50,
        public readonly int $offset = 0,
    ) {}
}
