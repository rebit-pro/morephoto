<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Controller;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Base;
use Psr\Container\NotFoundExceptionInterface;
use Rebit\Share\Application\Contract\Auth\TokenResolverInterface;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerInterface;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerTrait;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;
use Rebit\Share\Infrastructure\Controller\Filters\BearerTokenFilter;
use Rebit\Share\Infrastructure\Controller\Filters\LoggerFilter;

/**
 * Базовый контроллер для модуля Exchange.
 *
 * Включает авторизацию по Bearer-токену и логирование для всех экшенов.
 */
class BaseExchangeController extends BaseJsonController implements AuthenticatedControllerInterface
{
    use AuthenticatedControllerTrait;

    /**
     * @return Base[]
     *
     * @throws NotFoundExceptionInterface
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
