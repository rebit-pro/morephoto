<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final class SendMessageRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly int $tradeId,
        public readonly string $message,
        public readonly string $contentType = 'str',
        public readonly ?string $fileName = null,
    ) {}
}
