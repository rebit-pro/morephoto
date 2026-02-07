<?php

declare(strict_types=1);

use Bitrix\Main\SystemException;
use Bitrix\Main\InvalidOperationException;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleRoutingTrait;

/**
 * Модуль для работы с p2p\-торговлей на Bybit: стакан (фиат \u2194 крипто), балансы, отчёты и история сделок.
 */
class Rebit_Bybit extends CModule
{
    use ModuleRoutingTrait;

    public $MODULE_ID = 'rebit.bybit';
    public $MODULE_NAME = 'rebit\.bybit — P2P\-торговля на Bybit';
    public $MODULE_DESCRIPTION = 'Модуль интеграции с Bybit P2P: стакан заявок (покупка/продажа фиат \u2194 крипто), балансы пользователя, история сделок, отчёт об обороте в фиате и криптовалютах.';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2026-02-07 00:00:00';
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