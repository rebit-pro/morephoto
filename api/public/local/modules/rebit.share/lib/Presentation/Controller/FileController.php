<?php

declare(strict_types=1);

namespace Rebit\Share\Presentation\Controller;

use Rebit\Share\Application\UseCase\UploadFileUseCase;
use Rebit\Share\Domain\File\Dto\Request\UploadRequestFileRequestDto;
use Rebit\Share\Domain\File\Exception\FileUploadFailedException;
use Rebit\Share\Domain\File\Exception\InvalidFileException;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;

final class FileController extends BaseJsonController
{
    public function __construct(
        private readonly UploadFileUseCase $uploadFile,
    ) {
        parent::__construct();
    }

    /**
     * @throws InvalidFileException
     * @throws FileUploadFailedException
     */
    public function uploadAction(UploadRequestFileRequestDto $dto): ControllerJson
    {
        return $this->json($this->uploadFile->handle($dto));
    }
}
