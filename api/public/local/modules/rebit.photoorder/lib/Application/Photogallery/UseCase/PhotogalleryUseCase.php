<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Application\Photogallery\UseCase;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Rebit\PhotoOrder\Domain\Photogallery\Dto\Request\PhotogalleryRequestDto;
use Rebit\PhotoOrder\Domain\Photogallery\Dto\Result\PhotogalleryResultDto;
use Rebit\PhotoOrder\Domain\Photogallery\Repository\PhotogalleryRepository;
use Rebit\PhotoOrder\Domain\Photogallery\Dto\Result\ItemDto;

final readonly class PhotogalleryUseCase
{
    public function __construct(
        private PhotogalleryRepository $repository,
    ) {}

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function execute(PhotogalleryRequestDto $dto): PhotogalleryResultDto
    {
        $photogallery = $this->repository->findByCode($dto->code);

        return new PhotogalleryResultDto(
            code: $photogallery?->getCode() ?? '',
            name: $photogallery?->getName() ?? '',
            photos: array_map(
                fn($photo) => new ItemDto(
                    src: '/upload/' . $photo->getFile()->getSubdir() . '/' . $photo->getFile()->getFileName(),
                ),
                $photogallery?->getPhotos()->getAll() ?? [],
            ),
        );
    }
}
