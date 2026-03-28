<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Message\TradeStatusChangedMessage;

/**
 * Handler очереди tradeEvent: обработка смены статуса сделки.
 *
 * TODO: Добавить бизнес-логику (уведомления, обновление баланса).
 */
final readonly class TradeStatusChangedMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(TradeStatusChangedMessage $message): void
    {
        $this->logger->info('TradeStatusChangedMessage получено', [
            'tradeId' => $message->tradeId,
            'oldStatus' => $message->oldStatus,
            'newStatus' => $message->newStatus,
        ]);
    }
}
