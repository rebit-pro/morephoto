<?php

declare(strict_types=1);

namespace Rebit\Wallet\Infrastructure\Balance\Messenger;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Application\Contract\Wallet\BalanceSyncPublisherInterface;

final readonly class BalanceSyncPublisher implements BalanceSyncPublisherInterface
{
    public function __construct(
        private MessagePublisherInterface $publisher,
    ) {}

    public function dispatch(AbstractMessage $message, int $deduplicateTime = 0): void
    {
        $this->publisher->dispatch($message, $deduplicateTime);
    }
}
