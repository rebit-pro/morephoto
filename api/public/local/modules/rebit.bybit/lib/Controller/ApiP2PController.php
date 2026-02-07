<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Controller;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Rebit\PhotoOrder\Application\Photogallery\UseCase\PhotogalleryUseCase;
use Rebit\PhotoOrder\Domain\Photogallery\Dto\Request\PhotogalleryRequestDto;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;

final class ApiP2PController extends BaseJsonController
{
    public function __construct(
        private readonly PhotogalleryUseCase $useCase,
    ) {
        parent::__construct();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function handleAction(PhotogalleryRequestDto $dto): ControllerJson
    {
        return $this->json($this->useCase->execute($dto));
    }
}
