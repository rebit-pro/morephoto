<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Exchange;

/**
 * Null-объект: возвращает null для всех курсов.
 * Используется как дефолтная реализация, пока rebit.exchange не перезапишет реальной.
 */
final readonly class NullCurrencyRubRateQuery implements CurrencyRubRateQueryInterface
{
    public function findApproximateRubRateByCurrencyCode(string $currencyCode): ?float
    {
        return null;
    }
}
