<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bitrix;

use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;

final class TradeChatUploadFileLocator
{
    /**
     * @return array{
     *     path: string,
     *     name: string,
     *     mimeType: string,
     * }
     */
    public function getById(int $fileId): array
    {
        /** @var array<string, mixed>|false $file */
        $file = \CFile::GetFileArray($fileId);

        if (false === $file) {
            throw new EntityNotFoundException('Файл не найден');
        }

        $src = (string)($file['SRC'] ?? '');
        $filePath = '' !== $src ? (string)($_SERVER['DOCUMENT_ROOT'] . $src) : '';

        if ('' === $filePath || !is_file($filePath) || !is_readable($filePath)) {
            throw new ValidationHttpException('Файл недоступен для загрузки');
        }

        $fileName = (string)($file['ORIGINAL_NAME'] ?? $file['FILE_NAME'] ?? '');
        $mimeType = (string)($file['CONTENT_TYPE'] ?? 'application/octet-stream');

        if ('' === $fileName) {
            throw new ValidationHttpException('Не удалось определить имя файла');
        }

        return [
            'path' => $filePath,
            'name' => $fileName,
            'mimeType' => $mimeType,
        ];
    }
}
