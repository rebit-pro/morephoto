<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\Advertisement\Dto\Request\CreateAdvertisementRequestDto;
use Rebit\Exchange\Application\Advertisement\UseCase\CreateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\DeactivateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\ListAdvertisementsUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;

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
     */
    public function listAction(?string $status = null): ControllerJson
    {
        return $this->json(
            $this->listUseCase->execute($this->getAuthUserId(), $status),
        );
    }

    /**
     * POST /api/v1/exchange/advertisements
     */
    public function createAction(CreateAdvertisementRequestDto $dto): ControllerJson
    {
        return $this->json(
            $this->createUseCase->execute($dto, $this->getAuthUserId()),
        );
    }

    /**
     * DELETE /api/v1/exchange/advertisements/{id}
     */
    public function deleteAction(int $id): ControllerJson
    {
        $this->deactivateUseCase->execute($id, $this->getAuthUserId());

        return $this->json([]);
    }
}
