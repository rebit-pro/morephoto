<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Domain\Photogallery\Entity\Table;

use Bitrix\Iblock\Elements\ElementGalleryTable;
use Rebit\PhotoOrder\Domain\Photogallery\Entity\Photogallery;
use Rebit\PhotoOrder\Domain\Photogallery\Entity\PhotogalleryCollection;

final class PhotogalleryTable extends ElementGalleryTable
{
    public static function getObjectClass(): string
    {
        return Photogallery::class;
    }

    public static function getCollectionClass(): string
    {
        return PhotogalleryCollection::class;
    }
}
