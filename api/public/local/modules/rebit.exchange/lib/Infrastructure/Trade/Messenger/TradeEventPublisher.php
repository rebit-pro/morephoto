<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Trade\Messenger;

use Rebit\Exchange\Application\Trade\Port\TradeEventPublisherInterface;
use Rebit\Share\Application\Contract\Messenger\AbstractMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;

final readonly class TradeEventPublisher implements TradeEventPublisherInterface
{
    public function __construct(
        private MessagePublisherInterface $publisher,
    ) {}

    public function dispatch(AbstractMessage $message, int $deduplicateTime = 0): void
    {
        $this->publisher->dispatch($message, $deduplicateTime);
    }
}
