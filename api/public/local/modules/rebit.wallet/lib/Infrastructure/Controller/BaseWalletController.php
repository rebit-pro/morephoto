<?php

declare(strict_types=1);

namespace Rebit\Wallet\Infrastructure\Controller;

use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Engine\ActionFilter\Base;
use Rebit\Share\Application\Contract\Auth\TokenResolverInterface;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerInterface;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerTrait;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;
use Rebit\Share\Infrastructure\Controller\Filters\BearerTokenFilter;
use Rebit\Share\Infrastructure\Controller\Filters\LoggerFilter;

/**
 * Базовый контроллер для модуля Wallet.
 *
 * Включает авторизацию по Bearer-токену и логирование для всех экшенов.
 */
abstract class BaseWalletController extends BaseJsonController implements AuthenticatedControllerInterface
{
    use AuthenticatedControllerTrait;

    public function __construct()
    {
        parent::__construct();
    }

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
