<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bitrix;

use Rebit\Exchange\Application\TradeChat\Port\TradeChatUploadFileLocatorInterface;
use Rebit\Share\Domain\File\Service\UploadedFileOwnershipService;
use Rebit\Share\Shared\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\ValidationHttpException;

final readonly class TradeChatUploadFileLocator implements TradeChatUploadFileLocatorInterface
{
    private const string ALLOWED_UPLOAD_DIR = '/upload/rebit.exchange/';
    private const int MAX_FILE_SIZE_BYTES = 10 * 1024 * 1024;

    /** @var array<int, string> */
    private const array ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'application/pdf',
        'video/mp4',
        'video/quicktime',
        'video/webm',
    ];

    public function __construct(
        private UploadedFileOwnershipService $ownershipService,
    ) {}

    /**
     * @return array{
     *     path: string,
     *     name: string,
     *     mimeType: string,
     *     size: int,
     * }
     */
    public function getById(int $fileId, int $userId, string $moduleId): array
    {
        $ownership = $this->ownershipService->resolve($fileId);

        if (null === $ownership) {
            throw new HttpException('Файл недоступен для текущей сессии загрузки', 403);
        }

        if ($ownership['userId'] !== $userId || $ownership['moduleId'] !== $moduleId) {
            throw new HttpException('Нет доступа к загруженному файлу', 403);
        }

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

        if (!str_contains($src, self::ALLOWED_UPLOAD_DIR)) {
            throw new ValidationHttpException('Файл не относится к разрешённой загрузке чата');
        }

        $fileName = (string)($file['ORIGINAL_NAME'] ?? $file['FILE_NAME'] ?? '');
        $mimeType = (string)($file['CONTENT_TYPE'] ?? 'application/octet-stream');
        $fileSize = (int)($file['FILE_SIZE'] ?? $file['FILE_SIZE_RAW'] ?? (filesize($filePath) ?: 0));

        if ('' === $fileName) {
            throw new ValidationHttpException('Не удалось определить имя файла');
        }

        if (false === in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw new ValidationHttpException('Тип файла не поддерживается для чата');
        }

        if (0 >= $fileSize || self::MAX_FILE_SIZE_BYTES < $fileSize) {
            throw new ValidationHttpException('Размер файла превышает допустимый лимит');
        }

        return [
            'path' => $filePath,
            'name' => $fileName,
            'mimeType' => $mimeType,
            'size' => $fileSize,
        ];
    }
}
