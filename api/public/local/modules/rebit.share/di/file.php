<?php

declare(strict_types=1);

use Bitrix\Main\Application;
use Bitrix\Main\DI\ServiceLocator;
use Rebit\Share\Application\UseCase\UploadFileUseCase;
use Rebit\Share\Domain\File\Service\FileUploadService;
use Rebit\Share\Domain\File\Service\UploadedFileOwnershipService;
use Rebit\Share\Presentation\Controller\FileController;

return [
    UploadedFileOwnershipService::class => [
        'constructor' => static function(): UploadedFileOwnershipService {
            return new UploadedFileOwnershipService(
                Application::getInstance()->getManagedCache(),
            );
        },
    ],
    FileUploadService::class => [
        'constructor' => static function(): FileUploadService {
            return new FileUploadService(
                ServiceLocator::getInstance()->get(UploadedFileOwnershipService::class),
            );
        },
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
