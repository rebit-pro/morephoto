<?php

declare(strict_types=1);

use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Application\Contract\Exchange\NullCurrencyRubRateQuery;

return [
    CurrencyRubRateQueryInterface::class => [
        'constructor' => static fn(): CurrencyRubRateQueryInterface => new NullCurrencyRubRateQuery(),
    ],
];
