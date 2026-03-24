<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Application\UseCase\UploadFileUseCase;
use Rebit\Share\Domain\File\Service\FileUploadService;
use Rebit\Share\Presentation\Controller\FileController;

return [
    FileUploadService::class => [
        'className' => FileUploadService::class,
    ],
    UploadFileUseCase::class => [
        'constructor' => static function(): UploadFileUseCase {
            return new UploadFileUseCase(
                ServiceLocator::getInstance()->get(FileUploadService::class),
            );
        },
    ],
    FileController::class => [
        'constructor' => static function(): FileController {
            return new FileController(
                ServiceLocator::getInstance()->get(UploadFileUseCase::class),
            );
        },
    ],
];
