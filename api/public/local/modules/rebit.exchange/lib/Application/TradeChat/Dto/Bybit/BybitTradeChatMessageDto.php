<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Bybit;

final readonly class BybitTradeChatMessageDto
{
    public function __construct(
        public string $id,
        public string $message,
        public string $contentType,
        public string $fileName,
        public string $userId,
        public string $nickName,
        public string $createDate,
    ) {}
}
