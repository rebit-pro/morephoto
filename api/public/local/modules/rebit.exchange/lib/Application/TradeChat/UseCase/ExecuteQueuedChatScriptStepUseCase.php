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
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
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

        if (ExecutionStatusEnum::Pending->value !== $execution->getUfStatus()) {
            $this->logger->info('Пропущено выполнение шага чат-скрипта: execution уже не pending', [
                'executionId' => $execution->getId(),
                'status' => $execution->getUfStatus(),
            ]);

            return;
        }

        if ($execution->getUfTradeId() !== $message->tradeId) {
            $this->logger->warning('Пропущено выполнение шага чат-скрипта: tradeId в сообщении не совпадает с execution', [
                'executionId' => $execution->getId(),
                'messageTradeId' => $message->tradeId,
                'executionTradeId' => $execution->getUfTradeId(),
            ]);

            return;
        }

        $trade = $this->tradeRepository->findById($execution->getUfTradeId());
        if (null === $trade || !$this->isChatActive($trade)) {
            $execution->setUfStatus(ExecutionStatusEnum::Cancelled->value);
            $this->executionRepository->save($execution);

            $this->logger->info('Исполнение чат-скрипта отменено: сделка не найдена или чат уже неактивен', [
                'executionId' => $execution->getId(),
                'tradeId' => $execution->getUfTradeId(),
            ]);

            return;
        }

        $step = $this->stepRepository->findById($message->stepId);
        if (null === $step || $step->getUfScriptId() !== $execution->getUfScriptId()) {
            $this->logger->warning('Пропущено выполнение шага чат-скрипта: шаг не найден или не принадлежит скрипту execution', [
                'executionId' => $execution->getId(),
                'stepId' => $message->stepId,
                'scriptId' => $execution->getUfScriptId(),
            ]);

            return;
        }

        if ($step->getUfSort() <= $execution->getUfLastStepSort()) {
            $this->logger->info('Пропущено выполнение шага чат-скрипта: шаг уже был обработан', [
                'executionId' => $execution->getId(),
                'stepId' => $step->getId(),
                'stepSort' => $step->getUfSort(),
                'lastStepSort' => $execution->getUfLastStepSort(),
            ]);

            return;
        }

        if (0 < $message->delaySeconds) {
            sleep($message->delaySeconds);
        }

        $msgUuid = Uuid::uuid4()->toString();
        $this->chatGateway->sendMessage(
            $execution->getUfUserId(),
            $trade->getUfBybitOrderId(),
            $step->getUfMessage(),
            ContentTypeEnum::Str->value,
            $msgUuid,
        );

        $this->messageRepository->create(
            tradeId: $execution->getUfTradeId(),
            userId: $execution->getUfUserId(),
            message: $step->getUfMessage(),
            messageType: MessageTypeEnum::Script,
            contentType: ContentTypeEnum::Str,
            bybitMsgUuid: $msgUuid,
            scriptStepId: $step->getId(),
        );

        $execution->setUfLastStepSort($step->getUfSort());

        $steps = $this->stepRepository->findByScriptId($execution->getUfScriptId());
        $nextStep = null;
        foreach ($steps as $candidateStep) {
            if ($candidateStep->getUfSort() > $step->getUfSort()) {
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

        $this->chatScriptStepPublisher->dispatch(
            new ExecuteChatScriptStepMessage(
                executionId: $execution->getId(),
                tradeId: $execution->getUfTradeId(),
                stepId: $nextStep->getId(),
                delaySeconds: $nextStep->getUfDelaySeconds(),
            ),
        );

        $this->logger->info('Шаг чат-скрипта выполнен и следующий шаг поставлен в очередь', [
            'executionId' => $execution->getId(),
            'tradeId' => $execution->getUfTradeId(),
            'stepId' => $step->getId(),
            'nextStepId' => $nextStep->getId(),
            'nextDelaySeconds' => $nextStep->getUfDelaySeconds(),
        ]);
    }

    private function isChatActive(Trade $trade): bool
    {
        $status = TradeStatusEnum::tryFrom($trade->getUfStatus());

        return null !== $status && $status->isChatActive();
    }
}
