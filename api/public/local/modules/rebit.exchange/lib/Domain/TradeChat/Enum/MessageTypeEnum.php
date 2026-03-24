<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\TradeChat\Enum;

enum MessageTypeEnum: string
{
    case User = 'user';
    case System = 'system';
    case Script = 'script';
}
