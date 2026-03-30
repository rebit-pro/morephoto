<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Exchange;

use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Контракт для получения приблизительного курса валюты к RUB.
 * Реализуется в rebit.exchange, потребляется в rebit.wallet.
 */
interface CurrencyRubRateQueryInterface
{
    /**
     * Возвращает приблизительный курс 1 единицы валюты в RUB.
     *
     * @throws RepositoryException
     */
    public function findApproximateRubRateByCurrencyCode(string $currencyCode): ?float;
}
