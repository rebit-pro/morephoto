<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class TradeMessageResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public int $tradeId,
        public int $userId,
        public string $message,
        public string $messageType,
        public string $contentType,
        public ?string $fileName,
        public ?string $createdAt,
    ) {}
}
