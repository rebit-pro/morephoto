<?php

declare(strict_types=1);

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

Loader::includeModule('iblock');

/**
 * Модули от которых зависит этот модуль, падаем если не в адмнке.
 */
if (
    !Loader::includeModule('rebit.share')
    && !Application::getInstance()->getContext()->getRequest()->isAdminSection()
) {
    throw new RuntimeException('Module "rebit.share" is not installed!');
}
