<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Wallet;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;
use Rebit\Share\Application\Contract\Wallet\BalanceSyncPublisherInterface;

final readonly class NullBalanceSyncPublisher implements BalanceSyncPublisherInterface
{
    public function dispatch(AbstractMessage $message, int $deduplicateTime = 0): void {}
}
