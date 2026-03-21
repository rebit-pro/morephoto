<?php

declare(strict_types=1);

namespace Rebit\Identity\Infrastructure\Controller;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Base;
use Rebit\Share\Application\Contract\Auth\TokenResolverInterface;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerInterface;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerTrait;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;
use Rebit\Share\Infrastructure\Controller\Filters\BearerTokenFilter;
use Rebit\Share\Infrastructure\Controller\Filters\LoggerFilter;

/**
 * Базовый контроллер для модуля Identity.
 *
 * Включает авторизацию по Bearer-токену и логирование.
 */
class BaseIdentityController extends BaseJsonController implements AuthenticatedControllerInterface
{
    use AuthenticatedControllerTrait;

    /**
     * @return Base[]
     */
    protected function getDefaultPreFilters(): array
    {
        return [
            new BearerTokenFilter(
                ServiceLocator::getInstance()->get(TokenResolverInterface::class),
            ),
            new LoggerFilter(),
        ];
    }
}
