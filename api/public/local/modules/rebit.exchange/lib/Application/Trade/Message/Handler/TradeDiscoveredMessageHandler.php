<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Message\TradeDiscoveredMessage;

/**
 * Handler очереди tradeEvent: обработка обнаружения сделки.
 *
 * TODO: Добавить бизнес-логику (синхронизация контрагента, запуск чат-скрипта).
 */
final readonly class TradeDiscoveredMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(TradeDiscoveredMessage $message): void
    {
        $this->logger->info('TradeDiscoveredMessage получено', [
            'tradeId' => $message->tradeId,
            'bybitOrderId' => $message->bybitOrderId,
        ]);
    }
}
