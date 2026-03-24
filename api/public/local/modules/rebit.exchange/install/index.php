<?php

declare(strict_types=1);

use Bitrix\Main\SystemException;
use Bitrix\Main\InvalidOperationException;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleRoutingTrait;

/**
 * Модуль P2P-торговли: стаканы, объявления, сделки, чат, скрипты
 */
class Rebit_Exchange extends CModule
{
    use ModuleRoutingTrait;

    public $MODULE_ID = 'rebit.exchange';
    public $MODULE_NAME = 'rebit.exchange — P2P-торговля';
    public $MODULE_DESCRIPTION = 'Модуль P2P-торговли: стаканы ордеров, объявления, сделки, чат сделки, скрипты автосообщений';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2026-03-24 12:00:00';
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
