<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Transaction\ValueObject;

/**
 * Доменный фильтр транзакций.
 */
final readonly class TransactionFilter
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
