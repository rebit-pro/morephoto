<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleHelper;

Loader::includeModule('highloadblock');

ModuleHelper::validateModuleInstalled('rebit.share');

ModuleHelper::compileHLEntities(['RebitApiConnection']);
