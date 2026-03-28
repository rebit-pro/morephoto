<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Bitrix\Main\Type\DateTime;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Domain\ChatScript\Enum\ExecutionStatusEnum;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class ExecuteQueuedChatScriptStepUseCase
{
    public function __construct(
        private ChatScriptExecutionRepository $executionRepository,
        private ChatScriptStepRepository $stepRepository,
        private TradeRepository $tradeRepository,
        private TradeMessageRepository $messageRepository,
        private BybitChatGatewayInterface $chatGateway,
        private MessagePublisherInterface $chatScriptStepPublisher,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws RepositoryException
     * @throws HttpException
     */
    public function execute(ExecuteChatScriptStepMessage $message): void
    {
        $execution = $this->executionRepository->findById($message->executionId);
        if (null === $execution) {
            $this->logger->warning('Пропущено выполнение шага чат-скрипта: execution не найден', [
                'executionId' => $message->executionId,
                'tradeId' => $message->tradeId,
                'stepId' => $message->stepId,
            ]);

            return;
        }

        $executionStatus = (string)$execution->getUfStatus();
        if (ExecutionStatusEnum::Pending->value !== $executionStatus) {
            $this->logger->info('Пропущено выполнение шага чат-скрипта: execution уже не pending', [
                'executionId' => $execution->getId(),
                'status' => $executionStatus,
            ]);

            return;
        }

        $executionTradeId = (int)$execution->getUfTradeId();
        if ($executionTradeId !== $message->tradeId) {
            $this->logger->warning('Пропущено выполнение шага чат-скрипта: tradeId в сообщении не совпадает с execution', [
                'executionId' => $execution->getId(),
                'messageTradeId' => $message->tradeId,
                'executionTradeId' => $executionTradeId,
            ]);

            return;
        }

        $trade = $this->tradeRepository->findById($executionTradeId);
        $tradeStatus = null === $trade ? null : TradeStatusEnum::tryFrom($trade->getUfStatus());

        if (null === $trade || null === $tradeStatus || !$tradeStatus->isChatActive()) {
            $execution->setUfStatus(ExecutionStatusEnum::Cancelled->value);
            $this->executionRepository->save($execution);

            $this->logger->info('Исполнение чат-скрипта отменено: сделка не найдена или чат уже неактивен', [
                'executionId' => $execution->getId(),
                'tradeId' => $executionTradeId,
            ]);

            return;
        }

        $executionScriptId = (int)$execution->getUfScriptId();
        $step = $this->stepRepository->findById($message->stepId);
        if (null === $step) {
            $this->logger->warning('Пропущено выполнение шага чат-скрипта: шаг не найден или не принадлежит скрипту execution', [
                'executionId' => $execution->getId(),
                'stepId' => $message->stepId,
                'scriptId' => $executionScriptId,
            ]);

            return;
        }

        $stepScriptId = (int)$step->getUfScriptId();
        if ($stepScriptId !== $executionScriptId) {
            $this->logger->warning('Пропущено выполнение шага чат-скрипта: шаг не найден или не принадлежит скрипту execution', [
                'executionId' => $execution->getId(),
                'stepId' => $message->stepId,
                'scriptId' => $executionScriptId,
            ]);

            return;
        }

        $stepSort = (int)$step->getUfSort();
        $lastStepSort = (int)$execution->getUfLastStepSort();
        if ($stepSort <= $lastStepSort) {
            $this->logger->info('Пропущено выполнение шага чат-скрипта: шаг уже был обработан', [
                'executionId' => $execution->getId(),
                'stepId' => $step->getId(),
                'stepSort' => $stepSort,
                'lastStepSort' => $lastStepSort,
            ]);

            return;
        }

        $nextRunAt = $execution->getUfNextRunAt();
        if ($nextRunAt->getTimestamp() > time()) {
            $this->logger->info('Пропущено выполнение шага чат-скрипта: время шага ещё не наступило', [
                'executionId' => $execution->getId(),
                'stepId' => $step->getId(),
                'nextRunAt' => $nextRunAt->format('c'),
            ]);

            return;
        }

        $msgUuid = Uuid::uuid4()->toString();
        $executionUserId = (int)$execution->getUfUserId();

        $this->chatGateway->sendMessage(
            $executionUserId,
            $trade->getUfBybitOrderId(),
            $step->getUfMessage(),
            ContentTypeEnum::Str->value,
            $msgUuid,
        );

        $this->messageRepository->create(
            tradeId: $executionTradeId,
            userId: $executionUserId,
            message: $step->getUfMessage(),
            messageType: MessageTypeEnum::Script,
            contentType: ContentTypeEnum::Str,
            bybitMsgUuid: $msgUuid,
            scriptStepId: $step->getId(),
        );

        $execution->setUfLastStepSort($stepSort);

        $steps = $this->stepRepository->findByScriptId($executionScriptId);
        $nextStep = null;
        foreach ($steps as $candidateStep) {
            if ($candidateStep->getUfSort() > $stepSort) {
                $nextStep = $candidateStep;
                break;
            }
        }

        if (null === $nextStep) {
            $execution->setUfStatus(ExecutionStatusEnum::Completed->value);
            $this->executionRepository->save($execution);

            $this->logger->info('Исполнение чат-скрипта завершено', [
                'executionId' => $execution->getId(),
                'tradeId' => $execution->getUfTradeId(),
                'stepId' => $step->getId(),
            ]);

            return;
        }

        $execution->setUfNextRunAt((new DateTime())->add('+' . $nextStep->getUfDelaySeconds() . ' seconds'));
        $this->executionRepository->save($execution);

        if (0 === $nextStep->getUfDelaySeconds()) {
            $this->chatScriptStepPublisher->dispatch(
                new ExecuteChatScriptStepMessage(
                    executionId: (int)$execution->getId(),
                    tradeId: $executionTradeId,
                    stepId: $nextStep->getId(),
                    delaySeconds: 0,
                ),
            );

            $this->logger->info('Шаг чат-скрипта выполнен и следующий шаг поставлен в очередь', [
                'executionId' => $execution->getId(),
                'tradeId' => $executionTradeId,
                'stepId' => $step->getId(),
                'nextStepId' => $nextStep->getId(),
                'nextDelaySeconds' => $nextStep->getUfDelaySeconds(),
            ]);

            return;
        }

        $this->logger->info('Шаг чат-скрипта выполнен и следующий шаг запланирован', [
            'executionId' => $execution->getId(),
            'tradeId' => $executionTradeId,
            'stepId' => $step->getId(),
            'nextStepId' => $nextStep->getId(),
            'nextDelaySeconds' => $nextStep->getUfDelaySeconds(),
            'nextRunAt' => $execution->getUfNextRunAt()->format('c'),
        ]);
    }
}
