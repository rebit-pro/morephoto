<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Bitrix\Main\Type\DateTime;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecution;
use Rebit\Exchange\Domain\ChatScript\Enum\ExecutionStatusEnum;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Обработка очереди отложенных чат-скриптов.
 *
 * Забирает pending-исполнения с наступившим next_run_at,
 * отправляет следующий шаг скрипта в Bybit и планирует время следующего.
 */
final readonly class ProcessPendingChatScriptsUseCase
{
    public function __construct(
        private ChatScriptExecutionRepository $executionRepository,
        private ChatScriptStepRepository $stepRepository,
        private TradeRepository $tradeRepository,
        private TradeMessageRepository $messageRepository,
        private BybitChatGatewayInterface $chatGateway,
        private LoggerInterface $logger,
    ) {}

    /**
     * @return array{int, int, int} [sentCount, completedCount, errorCount]
     */
    public function execute(): array
    {
        $executions = $this->executionRepository->findReadyToProcess();

        $sentCount = 0;
        $completedCount = 0;
        $errorCount = 0;

        foreach ($executions as $execution) {
            try {
                $result = $this->processExecution($execution);

                if (null === $result) {
                    ++$completedCount;
                } else {
                    ++$sentCount;
                }
            } catch (\Throwable $e) {
                ++$errorCount;
                $this->logger->warning('Chat script execution failed', [
                    'executionId' => $execution->getId(),
                    'tradeId' => $execution->getUfTradeId(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [$sentCount, $completedCount, $errorCount];
    }

    /**
     * @return null|bool true = шаг отправлен, null = исполнение завершено/отменено
     *
     * @throws HttpException
     */
    private function processExecution(ChatScriptExecution $execution): ?bool
    {
        $trade = $this->tradeRepository->findById($execution->getUfTradeId());

        if (null === $trade || !$this->isChatActive($trade)) {
            $execution->setUfStatus(ExecutionStatusEnum::Cancelled->value);
            $this->executionRepository->save($execution);

            return null;
        }

        $steps = $this->stepRepository->findByScriptId($execution->getUfScriptId());
        $nextStep = null;
        $stepAfterNext = null;

        foreach ($steps as $step) {
            if ($step->getUfSort() > $execution->getUfLastStepSort()) {
                if (null === $nextStep) {
                    $nextStep = $step;
                } else {
                    $stepAfterNext = $step;

                    break;
                }
            }
        }

        if (null === $nextStep) {
            $execution->setUfStatus(ExecutionStatusEnum::Completed->value);
            $this->executionRepository->save($execution);

            return null;
        }

        $msgUuid = Uuid::uuid4()->toString();

        $this->chatGateway->sendMessage(
            $execution->getUfUserId(),
            $trade->getUfBybitOrderId(),
            $nextStep->getUfMessage(),
            ContentTypeEnum::Str->value,
            $msgUuid,
        );

        $this->messageRepository->create(
            tradeId: $execution->getUfTradeId(),
            userId: $execution->getUfUserId(),
            message: $nextStep->getUfMessage(),
            messageType: MessageTypeEnum::Script,
            contentType: ContentTypeEnum::Str,
            bybitMsgUuid: $msgUuid,
            scriptStepId: $nextStep->getId(),
        );

        $execution->setUfLastStepSort($nextStep->getUfSort());

        if (null !== $stepAfterNext) {
            $nextRunAt = (new DateTime())->add("+{$stepAfterNext->getUfDelaySeconds()} seconds");
            $execution->setUfNextRunAt($nextRunAt);
        } else {
            $execution->setUfStatus(ExecutionStatusEnum::Completed->value);
        }

        $this->executionRepository->save($execution);

        return true;
    }

    private function isChatActive(Trade $trade): bool
    {
        $status = TradeStatusEnum::tryFrom($trade->getUfStatus());

        return null !== $status && $status->isChatActive();
    }
}
