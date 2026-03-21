<?php

declare(strict_types=1);

namespace Rebit\Identity\Controller;

use Rebit\Identity\Application\ApiConnection\UseCase\ConnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\DisconnectApiUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\GetConnectionStatusUseCase;
use Rebit\Identity\Application\ApiConnection\UseCase\VerifyApiUseCase;
use Rebit\Identity\Domain\ApiConnection\Dto\Request\ConnectApiRequestDto;
use Rebit\Identity\Infrastructure\Controller\BaseIdentityController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Shared\Exception\HttpException;

final class ApiConnectionController extends BaseIdentityController
{
    public function __construct(
        private readonly ConnectApiUseCase $connectUseCase,
        private readonly DisconnectApiUseCase $disconnectUseCase,
        private readonly VerifyApiUseCase $verifyUseCase,
        private readonly GetConnectionStatusUseCase $getStatusUseCase,
    ) {
        parent::__construct();
    }

    /**
     * POST /api/v1/identity/connection
     *
     * @throws HttpException
     * @throws \Exception
     */
    public function connectAction(ConnectApiRequestDto $dto): ControllerJson
    {
        return $this->json(
            $this->connectUseCase->execute($dto, $this->getAuthUserId()),
        );
    }

    /**
     * DELETE /api/v1/identity/connection
     *
     * @throws HttpException
     * @throws \Exception
     */
    public function disconnectAction(): ControllerJson
    {
        $this->disconnectUseCase->execute($this->getAuthUserId());

        return $this->json([]);
    }

    /**
     * POST /api/v1/identity/connection/verify
     *
     * @throws HttpException
     * @throws \Exception
     */
    public function verifyAction(): ControllerJson
    {
        return $this->json(
            $this->verifyUseCase->execute($this->getAuthUserId()),
        );
    }

    /**
     * GET /api/v1/identity/connection/status
     *
     * @throws HttpException
     */
    public function statusAction(): ControllerJson
    {
        return $this->json(
            $this->getStatusUseCase->execute($this->getAuthUserId()),
        );
    }
}
