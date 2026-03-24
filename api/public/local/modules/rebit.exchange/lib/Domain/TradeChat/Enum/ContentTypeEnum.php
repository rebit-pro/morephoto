<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\TradeChat\Enum;

enum ContentTypeEnum: string
{
    case Str = 'str';
    case Pic = 'pic';
    case Pdf = 'pdf';
    case Video = 'video';
}
