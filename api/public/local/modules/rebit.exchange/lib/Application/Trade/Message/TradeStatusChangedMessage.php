<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Message;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;

/**
 * Сообщение: изменился статус сделки.
 */
final readonly class TradeStatusChangedMessage extends AbstractMessage
{
    public function __construct(
        public int $tradeId,
        public string $oldStatus,
        public string $newStatus,
    ) {
        parent::__construct();
    }
}
