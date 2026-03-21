<?php

declare(strict_types=1);

use Bitrix\Main\SystemException;
use Bitrix\Main\InvalidOperationException;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleRoutingTrait;

/**
 * Модуль HTTP-клиента Bybit API
 */
class Rebit_Bybit extends CModule
{
    use ModuleRoutingTrait;

    public $MODULE_ID = 'rebit.bybit';
    public $MODULE_NAME = 'rebit.bybit — HTTP-клиент Bybit API';
    public $MODULE_DESCRIPTION = 'Инфраструктурный модуль для HTTP-взаимодействия с Bybit API (HMAC-аутентификация, подпись запросов)';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2026-03-20 12:00:00';
    public $PARTNER_NAME = 'rebit';
    public $PARTNER_URI = 'https://rebit-pro.ru';

    /**
     * @throws SystemException
     */
    public function __construct()
    {
        $this->initModuleRouting();
    }

    /**
     * @throws InvalidOperationException
     * @throws SystemException
     */
    public function DoInstall(): bool
    {
        RegisterModule($this->MODULE_ID);
        $this->installModuleRouting();

        return true;
    }

    /**
     * @throws InvalidOperationException
     * @throws SystemException
     */
    public function DoUninstall(): bool
    {
        $this->uninstallModuleRouting();
        UnRegisterModule($this->MODULE_ID);

        return true;
    }
}
