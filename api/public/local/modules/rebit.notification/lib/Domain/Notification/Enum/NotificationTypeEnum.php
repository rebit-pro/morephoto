<?php

declare(strict_types=1);

namespace Rebit\Notification\Domain\Notification\Enum;

/**
 * Типы уведомлений (определяют шаблон и логику отправки).
 */
enum NotificationTypeEnum: string
{
    case TRADE_DISCOVERED = 'tradeDiscovered';
    case TRADE_STATUS_CHANGED = 'tradeStatusChanged';
}
