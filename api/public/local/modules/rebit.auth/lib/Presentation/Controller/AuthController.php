<?php

declare(strict_types=1);

namespace Rebit\Auth\Presentation\Controller;

use Rebit\Auth\Application\Auth\Dto\Request\LoginRequestDto;
use Rebit\Auth\Application\Auth\UseCase\LoginUseCase;
use Rebit\Auth\Application\Auth\UseCase\LogoutUseCase;
use Rebit\Share\Application\Contract\Auth\TokenResolverInterface;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerInterface;
use Rebit\Share\Infrastructure\Controller\Auth\AuthenticatedControllerTrait;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;
use Rebit\Share\Infrastructure\Controller\Filters\BearerTokenFilter;
use Rebit\Share\Infrastructure\Controller\Filters\LoggerFilter;
use Rebit\Share\Shared\Exception\HttpException;
use Random\RandomException;

final class AuthController extends BaseJsonController implements AuthenticatedControllerInterface
{
    use AuthenticatedControllerTrait;

    public function __construct(
        private readonly LoginUseCase $loginUseCase,
        private readonly LogoutUseCase $logoutUseCase,
        private readonly TokenResolverInterface $tokenResolver,
    ) {
        parent::__construct();
    }

    /**
     * POST /api/v1/auth/login
     *
     * @throws HttpException
     * @throws RandomException
     */
    public function loginAction(LoginRequestDto $dto): ControllerJson
    {
        return $this->json(
            $this->loginUseCase->execute($dto),
        );
    }

    /**
     * POST /api/v1/auth/logout
     *
     * @throws HttpException
     */
    public function logoutAction(): ControllerJson
    {
        $this->logoutUseCase->execute($this->getAuthUserId());

        return $this->json([]);
    }

    /**
     * Фильтры по экшенам:
     *  - login: без авторизации (гостевой)
     *  - logout: требуется Bearer-токен
     */
    public function configureActions(): array
    {
        return [
            'login' => [
                'prefilters' => [
                    new LoggerFilter(),
                ],
            ],
            'logout' => [
                'prefilters' => [
                    new BearerTokenFilter($this->tokenResolver),
                    new LoggerFilter(),
                ],
            ],
        ];
    }
}
