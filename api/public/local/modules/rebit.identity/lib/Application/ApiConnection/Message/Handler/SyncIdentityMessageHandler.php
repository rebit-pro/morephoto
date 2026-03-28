<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Identity\Application\ApiConnection\Message\SyncIdentityMessage;

final readonly class SyncIdentityMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SyncIdentityMessage $message): void
    {
        $this->logger->info('SyncIdentityMessage получено', [
            'userId' => $message->userId,
        ]);
    }
}
