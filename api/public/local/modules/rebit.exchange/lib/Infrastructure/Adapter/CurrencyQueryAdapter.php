<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Adapter;

use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Адаптер для запроса ID валюты по коду.
 * Реализует контракт из rebit.share, делегирует запрос в CurrencyRepository.
 */
final readonly class CurrencyQueryAdapter implements CurrencyQueryInterface
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function findIdByCode(string $code): ?int
    {
        return $this->currencyRepository->findByCode($code)?->getId();
    }
}
