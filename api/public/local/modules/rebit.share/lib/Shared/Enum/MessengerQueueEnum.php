<?php

declare(strict_types=1);

namespace Rebit\Share\Shared\Enum;

enum MessengerQueueEnum: string
{
    case TRADE_EVENT = 'tradeEvent';
    case CHAT_SCRIPT_STEP = 'chatScriptStep';
    case NOTIFICATION = 'notification';
    case BALANCE_SYNC = 'balanceSync';
    case IDENTITY_SYNC = 'identitySync';
    case AUDIT = 'audit';
    case FAILED = 'messengerFailed';

    /**
     * Возвращает ключ сервиса транспорта в DI-контейнере.
     *
     * Сам `value` enum используется как имя очереди/роутинга в Messenger,
     * а транспорт для этой очереди регистрируется отдельным сервисом
     * с суффиксом `_transport`, например `tradeEvent_transport`.
     */
    public function transportKey(): string
    {
        return $this->value . '_transport';
    }
}
