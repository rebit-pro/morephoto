<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\Advertisement\Dto\Request\CreateAdvertisementRequestDto;
use Rebit\Exchange\Application\Advertisement\UseCase\CreateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\DeactivateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\ListAdvertisementsUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Bitrix\Main\HttpResponse;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final class AdvertisementController extends BaseExchangeController
{
    public function __construct(
        private readonly CreateAdvertisementUseCase $createUseCase,
        private readonly ListAdvertisementsUseCase $listUseCase,
        private readonly DeactivateAdvertisementUseCase $deactivateUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/exchange/advertisements
     * @throws HttpException
     * @throws RepositoryException
     * @throws \JsonException
     */
    public function listAction(?string $status = null): ControllerJson
    {
        return $this->json(
            $this->listUseCase->execute($this->getAuthUserId(), $status),
        );
    }

    /**
     * POST /api/v1/exchange/advertisements
     * @throws HttpException
     * @throws RepositoryException
     */
    public function createAction(CreateAdvertisementRequestDto $dto): ControllerJson
    {
        return $this->json(
            $this->createUseCase->execute($dto, $this->getAuthUserId()),
        );
    }

    /**
     * DELETE /api/v1/exchange/advertisements/{id}
     * @throws HttpException
     * @throws RepositoryException
     */
    public function deleteAction(int $id): HttpResponse
    {
        $this->deactivateUseCase->execute($id, $this->getAuthUserId());

        return $this->noContent();
    }
}
