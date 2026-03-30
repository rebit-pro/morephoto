<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Port;

interface TradeChatUploadFileLocatorInterface
{
    /**
     * @return array{
     *     path: string,
     *     name: string,
     *     mimeType: string,
     *     size: int,
     * }
     */
    public function getById(int $fileId, int $userId, string $moduleId): array;
}
