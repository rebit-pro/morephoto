<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Exchange;

use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Контракт для запроса ID валюты по её коду.
 * Реализуется в rebit.exchange, потребляется в rebit.wallet.
 */
interface CurrencyQueryInterface
{
    /**
     * Найти ID валюты по символьному коду (например, USDT, USDC).
     *
     * @throws RepositoryException
     */
    public function findIdByCode(string $code): ?int;

    /**
     * Найти символьный код валюты по ID.
     *
     * @throws RepositoryException
     */
    public function findCodeById(int $id): ?string;
}
