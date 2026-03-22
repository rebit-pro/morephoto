<?php

declare(strict_types=1);

/**
 * Модуль HTTP-клиента Bybit API
 */
class Rebit_Bybit extends CModule
{
    public $MODULE_ID = 'rebit.bybit';
    public $MODULE_NAME = 'rebit.bybit — HTTP-клиент Bybit API';
    public $MODULE_DESCRIPTION = 'Инфраструктурный модуль для HTTP-взаимодействия с Bybit API (HMAC-аутентификация, подпись запросов)';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2026-03-20 12:00:00';
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
