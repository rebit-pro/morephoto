<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Audit\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Share\Application\Audit\Message\AuditMessage;

final readonly class AuditMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(AuditMessage $message): void
    {
        $this->logger->info('AuditMessage получено', [
            'userId' => $message->userId,
            'action' => $message->action,
            'context' => $message->context,
        ]);
    }
}
