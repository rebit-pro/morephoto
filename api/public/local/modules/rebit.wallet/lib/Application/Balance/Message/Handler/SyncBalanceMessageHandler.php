<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Wallet\Application\Balance\Message\SyncBalanceMessage;

final readonly class SyncBalanceMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SyncBalanceMessage $message): void
    {
        $this->logger->info('SyncBalanceMessage получено', [
            'userId' => $message->userId,
            'currency' => $message->currency,
        ]);
    }
}
