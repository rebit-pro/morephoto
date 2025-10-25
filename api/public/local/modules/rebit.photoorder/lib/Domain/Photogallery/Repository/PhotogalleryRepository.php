<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Domain\Photogallery\Repository;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Rebit\PhotoOrder\Domain\Photogallery\Entity\Photogallery;
use Rebit\PhotoOrder\Domain\Photogallery\Entity\Table\PhotogalleryTable;


final class PhotogalleryRepository
{
    private const int TTL = 3600;

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function findByCode(string $code): ?Photogallery
    {
        return PhotogalleryTable::query()
            ->setSelect($this->getSelectFields())
            ->where('CODE', $code)
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetchObject()
        ;
    }

    private function getSelectFields(): array
    {
        return [
            'ID',
            'NAME',
            'CODE',
            'PHOTOS.FILE',
        ];
    }
}
