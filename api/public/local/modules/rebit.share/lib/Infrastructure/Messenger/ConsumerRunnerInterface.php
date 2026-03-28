<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

interface ConsumerRunnerInterface
{
    public function run(
        TransportInterface $transport,
        string $queueName,
        MessageBusInterface $bus,
        int $limit,
        int $timeLimit,
    ): void;
}
