<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Controller;

use Rebit\PhotoOrder\Application\Photogallery\UseCase\PhotogalleryUseCase;
use Rebit\PhotoOrder\Domain\Photogallery\Dto\Request\PhotogalleryRequestDto;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;

final class PhotoController extends BaseJsonController
{
    public function __construct(
        private readonly PhotogalleryUseCase $useCase,
    ) {
        parent::__construct();
    }

    public function handleAction(PhotogalleryRequestDto $dto): ControllerJson
    {
        return $this->json($this->useCase->execute($dto));
    }
}
