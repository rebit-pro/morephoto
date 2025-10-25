<?php

declare(strict_types=1);

class Rebit_Share extends CModule
{
    var $MODULE_ID = 'rebit.share';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    public function __construct()
    {
        $this->MODULE_VERSION = '1.0.0';
        $this->MODULE_VERSION_DATE = '2025-06-20 08:00:00';
        $this->MODULE_NAME = 'rebit.share';
        $this->MODULE_DESCRIPTION = 'rebit.share - Модуль с общей для других модулей инфраструктурной составляющей';
        $this->PARTNER_NAME = 'rebit-pro';
        $this->PARTNER_URI = 'https://rebit-pro.ru';
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
