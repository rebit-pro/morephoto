<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\PhotoOrder\Domain\Photogallery\Repository\PhotogalleryRepository;
use Rebit\PhotoOrder\Application\Photogallery\UseCase\PhotogalleryUseCase;

return [
    'services' => [
        'value' => [
            PhotogalleryRepository::class => [
                'className' => PhotogalleryRepository::class,
            ],
            PhotogalleryUseCase::class => [
                'className' => PhotogalleryUseCase::class,
                'constructorParams' => static function() {
                    return [
                        ServiceLocator::getInstance()->get(PhotogalleryRepository::class),
                    ];
                },
            ],
        ],
        'readonly' => true,
    ],
];
