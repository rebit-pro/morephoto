<?php

declare(strict_types=1);

/**
 * Автозагрузка lib находится в composer.
 * Нет необходимости устанавливать этот модуль.
 */
class Rebit_Dev extends CModule
{
    var $MODULE_ID = 'rebit.dev';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    public function __construct()
    {
        $this->MODULE_VERSION = '1.0.0';
        $this->MODULE_VERSION_DATE = '2025-06-20 08:00:00';
        $this->MODULE_NAME = 'rebit.dev';
        $this->MODULE_DESCRIPTION = 'Модуль включает в себя инструменты, которые используются для разработки и не требует установки';
        $this->PARTNER_NAME = 'rebit-pro';
        $this->PARTNER_URI = 'https://rebit.ru';
    }

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
