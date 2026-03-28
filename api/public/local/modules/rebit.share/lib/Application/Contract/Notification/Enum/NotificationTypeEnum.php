<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Notification\Enum;

/**
 * Типы уведомлений — межмодульный контракт.
 *
 * Определяет допустимые значения для поля `type` в SendNotificationDto.
 * Модуль rebit.notification может использовать свой доменный enum,
 * но значения должны совпадать.
 */
enum NotificationTypeEnum: string
{
    case TRADE_DISCOVERED = 'tradeDiscovered';
    case TRADE_STATUS_CHANGED = 'tradeStatusChanged';
}
