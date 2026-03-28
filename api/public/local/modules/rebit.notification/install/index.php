<?php

declare(strict_types=1);

/**
 * Модуль уведомлений (очереди, email-каналы)
 */
class Rebit_Notification extends CModule
{
    public $MODULE_ID = 'rebit.notification';
    public $MODULE_NAME = 'rebit.notification — Уведомления';
    public $MODULE_DESCRIPTION = 'Модуль уведомлений: очереди сообщений, email-каналы, consumer';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2026-03-28 12:00:00';
    public $PARTNER_NAME = 'rebit';
    public $PARTNER_URI = 'https://rebit-pro.ru';

    public function DoInstall(): bool
    {
        RegisterModule($this->MODULE_ID);

        return true;
    }

    public function DoUninstall(): bool
    {
        UnRegisterModule($this->MODULE_ID);

        return true;
    }
}
