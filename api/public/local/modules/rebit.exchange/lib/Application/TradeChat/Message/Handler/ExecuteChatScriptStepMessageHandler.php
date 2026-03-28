<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;

/**
 * Handler очереди chatScriptStep: выполнение шага чат-скрипта.
 *
 * TODO: Добавить реальную отправку шага через Bybit API.
 */
final readonly class ExecuteChatScriptStepMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ExecuteChatScriptStepMessage $message): void
    {
        $this->logger->info('ExecuteChatScriptStepMessage получено', [
            'executionId' => $message->executionId,
            'tradeId' => $message->tradeId,
            'stepId' => $message->stepId,
        ]);
    }
}
