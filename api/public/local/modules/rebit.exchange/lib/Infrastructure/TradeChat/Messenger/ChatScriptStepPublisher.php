<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\TradeChat\Messenger;

use Rebit\Exchange\Application\TradeChat\Port\ChatScriptStepPublisherInterface;
use Rebit\Share\Application\Contract\Messenger\AbstractMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;

final readonly class ChatScriptStepPublisher implements ChatScriptStepPublisherInterface
{
    public function __construct(
        private MessagePublisherInterface $publisher,
    ) {}

    public function dispatch(AbstractMessage $message, int $deduplicateTime = 0): void
    {
        $this->publisher->dispatch($message, $deduplicateTime);
    }
}
