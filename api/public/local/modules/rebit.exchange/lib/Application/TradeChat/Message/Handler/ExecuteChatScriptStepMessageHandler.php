<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;
use Rebit\Exchange\Application\TradeChat\UseCase\ExecuteQueuedChatScriptStepUseCase;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Handler очереди chatScriptStep: выполнение шага чат-скрипта.
 */
final readonly class ExecuteChatScriptStepMessageHandler
{
    public function __construct(
        private ExecuteQueuedChatScriptStepUseCase $executeQueuedChatScriptStepUseCase,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function __invoke(ExecuteChatScriptStepMessage $message): void
    {
        $this->executeQueuedChatScriptStepUseCase->execute($message);

        $this->logger->info('ExecuteChatScriptStepMessage получено', [
            'executionId' => $message->executionId,
            'tradeId' => $message->tradeId,
            'stepId' => $message->stepId,
            'delaySeconds' => $message->delaySeconds,
        ]);
    }
}
