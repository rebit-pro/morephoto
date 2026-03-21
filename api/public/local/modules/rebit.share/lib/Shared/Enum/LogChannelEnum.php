<?php

declare(strict_types=1);

namespace Rebit\Share\Shared\Enum;

enum LogChannelEnum: string
{
    case default = 'rebit';
    case exchange = 'exchange';
    case wallet = 'wallet';
    case identity = 'identity';
    case notification = 'notification';
    case security = 'security';
    case auth = 'auth';
    case bybit = 'bybit';
    case cli = 'cli';
    case todo = 'todo'; // канал для оценки, временного сбора информации и т.п.
    case payment = 'payment';
    case import = 'import'; // канал импортов
}
