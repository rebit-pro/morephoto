<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Bybit;

final readonly class BybitTradeChatUploadResultDto
{
    public function __construct(
        public string $url,
        public string $type,
    ) {}
}
