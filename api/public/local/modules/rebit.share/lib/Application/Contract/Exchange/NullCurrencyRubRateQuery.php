<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Exchange;

/**
 * Null-объект: возвращает безопасный fallback-курс.
 * Используется как дефолтная реализация, пока rebit.exchange не перезапишет реальной.
 */
final readonly class NullCurrencyRubRateQuery implements CurrencyRubRateQueryInterface
{
    public function findApproximateRubRateByCurrencyCode(string $currencyCode): ?float
    {
        if ('RUB' === strtoupper($currencyCode)) {
            return 1.0;
        }

        return null;
    }
}
