<?php

declare(strict_types=1);

namespace Rebit\Identity\Infrastructure\ApiConnection\Messenger;

use Rebit\Identity\Application\ApiConnection\Port\IdentitySyncPublisherInterface;
use Rebit\Share\Application\Contract\Messenger\AbstractMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;

final readonly class IdentitySyncPublisher implements IdentitySyncPublisherInterface
{
    public function __construct(
        private MessagePublisherInterface $publisher,
    ) {}

    public function dispatch(AbstractMessage $message, int $deduplicateTime = 0): void
    {
        $this->publisher->dispatch($message, $deduplicateTime);
    }
}
