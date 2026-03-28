<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Bybit;

final readonly class BybitTradeChatMessageListDto
{
    /**
     * @param list<BybitTradeChatMessageDto> $messages
     */
    public function __construct(
        public array $messages,
    ) {}
}
