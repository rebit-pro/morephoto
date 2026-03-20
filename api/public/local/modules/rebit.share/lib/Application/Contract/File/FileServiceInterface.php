<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\File;

/**
 * Интерфейс возвращает список ссылок на файлы по их ID
 */
interface FileServiceInterface
{
    /**
     * @param int[] $fileIds
     *
     * @return array<int, string> // ключ - ID файла
     */
    public function getListByIds(array $fileIds): array;
}
