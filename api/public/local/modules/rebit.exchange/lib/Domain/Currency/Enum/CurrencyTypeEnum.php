<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Currency\Enum;

enum CurrencyTypeEnum: string
{
    case Crypto = 'crypto';
    case Fiat = 'fiat';
}
