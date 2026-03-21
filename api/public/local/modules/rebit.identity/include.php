<?php

declare(strict_types=1);

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

if (
    !Loader::includeModule('rebit.share')
    && !Application::getInstance()->getContext()->getRequest()->isAdminSection()
) {
    throw new RuntimeException('Module "rebit.share" is not installed!');
}

if (
    !Loader::includeModule('rebit.auth')
    && !Application::getInstance()->getContext()->getRequest()->isAdminSection()
) {
    throw new RuntimeException('Module "rebit.auth" is not installed!');
}

if (
    !Loader::includeModule('rebit.bybit')
    && !Application::getInstance()->getContext()->getRequest()->isAdminSection()
) {
    throw new RuntimeException('Module "rebit.bybit" is not installed!');
}
