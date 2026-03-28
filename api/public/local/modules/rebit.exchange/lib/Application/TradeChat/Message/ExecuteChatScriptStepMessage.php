<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Message;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;

/**
 * Сообщение: выполнить шаг чат-скрипта.
 */
final readonly class ExecuteChatScriptStepMessage extends AbstractMessage
{
    public function __construct(
        public int $executionId,
        public int $tradeId,
        public int $stepId,
        public int $delaySeconds = 0,
    ) {
        parent::__construct();
    }
}
