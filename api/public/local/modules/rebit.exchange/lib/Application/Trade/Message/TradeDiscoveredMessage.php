<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Message;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;

/**
 * Сообщение: обнаружена новая P2P-сделка.
 */
final readonly class TradeDiscoveredMessage extends AbstractMessage
{
    public function __construct(
        public int $tradeId,
        public string $bybitOrderId,
        public string $fiatAmount,
    ) {
        parent::__construct();
    }
}
