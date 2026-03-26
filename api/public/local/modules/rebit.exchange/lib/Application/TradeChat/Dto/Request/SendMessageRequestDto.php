<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final readonly class SendMessageRequestDto implements RequestDtoInterface
{
    public function __construct(
        public int $tradeId,
        public string $message,
        public string $contentType = 'str',
        public ?string $fileName = null,
    ) {}
}
