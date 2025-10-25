<?php

declare(strict_types=1);

use Bitrix\Main\SystemException;
use Bitrix\Main\InvalidOperationException;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleRoutingTrait;

/**
 * Модуль для работы с ботами rebit
 */
class Rebit_Photoorder extends CModule
{
    use ModuleRoutingTrait;

    public $MODULE_ID = 'rebit.photoorder';
    public $MODULE_NAME = 'rebit.photoorder — Заказ печати фотографий';
    public $MODULE_DESCRIPTION = 'Модуль для выбора фотографий с указанием размера и количества для последующей печати';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2025-10-17 15:00:00';
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
