<?php

declare(strict_types=1);

use Bitrix\Main\InvalidOperationException;
use Bitrix\Main\SystemException;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleRoutingTrait;

/**
 * Модуль уведомлений (очереди, email-каналы, HTTP-приём заявок)
 */
class Rebit_Notification extends CModule
{
    use ModuleRoutingTrait;

    public $MODULE_ID = 'rebit.notification';
    public $MODULE_NAME = 'rebit.notification — Уведомления';
    public $MODULE_DESCRIPTION = 'Модуль уведомлений: очереди сообщений, email-каналы, consumer, приём заявок';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2026-03-28 12:00:00';
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
