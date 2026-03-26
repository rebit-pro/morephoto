<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Transaction\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Rebit\Wallet\Domain\Transaction\Enum\TransactionTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO фильтрации транзакций. Используется в контроллере через автомапинг.
 */
final readonly class TransactionFilterRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Choice(
            callback: [TransactionTypeEnum::class, 'values'],
            message: 'Некорректный тип транзакции.',
        )]
        public ?string $type = null,
        #[Assert\Positive(message: 'currencyId должен быть положительным числом.')]
        public ?int $currencyId = null,
        #[Assert\Date(message: 'dateFrom должен быть в формате Y-m-d.')]
        public ?string $dateFrom = null,
        #[Assert\Date(message: 'dateTo должен быть в формате Y-m-d.')]
        public ?string $dateTo = null,
        #[Assert\Positive(message: 'limit должен быть положительным числом.')]
        #[Assert\LessThanOrEqual(value: 1000, message: 'limit не может быть больше 1000.')]
        public int $limit = 50,
        #[Assert\GreaterThanOrEqual(value: 0, message: 'offset не может быть отрицательным.')]
        public int $offset = 0,
    ) {}
}
