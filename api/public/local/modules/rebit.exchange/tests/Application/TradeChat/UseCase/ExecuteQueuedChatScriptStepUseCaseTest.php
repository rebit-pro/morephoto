<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\Port\ChatScriptStepPublisherInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\ExecuteQueuedChatScriptStepUseCase;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecution;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Enum\ExecutionStatusEnum;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class ExecuteQueuedChatScriptStepUseCaseTest extends TestCase
{
    public function testCancelsExecutionWhenTradeChatIsInactive(): void
    {
        $execution = $this->createStub(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(7);
        $execution->method('getUfStatus')->willReturn(ExecutionStatusEnum::Pending->value);
        $execution->method('getUfTradeId')->willReturn(10);

        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);
        $executionRepository->method('findById')->willReturn($execution);

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfStatus')->willReturn('completed');

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($trade);

        $stepRepository = $this->createStub(ChatScriptStepRepository::class);
        $messageRepository = $this->createStub(TradeMessageRepository::class);
        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->never())->method('sendMessage');
        $publisher = $this->createMock(ChatScriptStepPublisherInterface::class);
        $publisher->expects($this->never())->method('dispatch');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $useCase = new ExecuteQueuedChatScriptStepUseCase(
            $executionRepository,
            $stepRepository,
            $tradeRepository,
            $messageRepository,
            $chatGateway,
            $publisher,
            $logger,
        );

        $useCase->execute(new ExecuteChatScriptStepMessage(executionId: 7, tradeId: 10, stepId: 11));
    }

    public function testSkipsExecutionWhenNextRunAtHasNotComeYet(): void
    {
        $execution = $this->createStub(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(7);
        $execution->method('getUfStatus')->willReturn(ExecutionStatusEnum::Pending->value);
        $execution->method('getUfTradeId')->willReturn(10);
        $execution->method('getUfScriptId')->willReturn(30);
        $execution->method('getUfLastStepSort')->willReturn(0);
        $execution->method('getUfNextRunAt')->willReturn(DateTime::createFromTimestamp(time() + 3600));

        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);
        $executionRepository->method('findById')->willReturn($execution);

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfStatus')->willReturn('pending_payment');

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($trade);

        $step = $this->createStub(ChatScriptStep::class);
        $step->method('getId')->willReturn(11);
        $step->method('getUfScriptId')->willReturn(30);
        $step->method('getUfSort')->willReturn(100);

        $stepRepository = $this->createStub(ChatScriptStepRepository::class);
        $stepRepository->method('findById')->willReturn($step);

        $messageRepository = $this->createStub(TradeMessageRepository::class);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->never())->method('sendMessage');

        $publisher = $this->createMock(ChatScriptStepPublisherInterface::class);
        $publisher->expects($this->never())->method('dispatch');

        $useCase = new ExecuteQueuedChatScriptStepUseCase(
            $executionRepository,
            $stepRepository,
            $tradeRepository,
            $messageRepository,
            $chatGateway,
            $publisher,
            new NullLogger(),
        );

        $useCase->execute(new ExecuteChatScriptStepMessage(executionId: 7, tradeId: 10, stepId: 11));
    }

    public function testDispatchesImmediateNextStep(): void
    {
        $execution = $this->createStub(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(7);
        $execution->method('getUfStatus')->willReturn(ExecutionStatusEnum::Pending->value);
        $execution->method('getUfTradeId')->willReturn(10);
        $execution->method('getUfScriptId')->willReturn(30);
        $execution->method('getUfUserId')->willReturn(42);
        $execution->method('getUfLastStepSort')->willReturn(0);
        $execution->method('getUfNextRunAt')->willReturn(DateTime::createFromTimestamp(time() - 60));

        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);
        $executionRepository->method('findById')->willReturn($execution);

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfStatus')->willReturn('pending_payment');
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($trade);

        $currentStep = $this->createStub(ChatScriptStep::class);
        $currentStep->method('getId')->willReturn(11);
        $currentStep->method('getUfScriptId')->willReturn(30);
        $currentStep->method('getUfSort')->willReturn(100);
        $currentStep->method('getUfMessage')->willReturn('Первый шаг');

        $nextStep = $this->createStub(ChatScriptStep::class);
        $nextStep->method('getId')->willReturn(12);
        $nextStep->method('getUfSort')->willReturn(200);
        $nextStep->method('getUfDelaySeconds')->willReturn(0);

        $steps = $this->createStub(ChatScriptStepCollection::class);
        $steps->method('getIterator')->willReturn(new \ArrayIterator([$currentStep, $nextStep]));

        $stepRepository = $this->createStub(ChatScriptStepRepository::class);
        $stepRepository->method('findById')->willReturn($currentStep);
        $stepRepository->method('findByScriptId')->willReturn($steps);

        $messageRepository = $this->createStub(TradeMessageRepository::class);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->once())->method('sendMessage');

        $publisher = $this->createMock(ChatScriptStepPublisherInterface::class);
        $publisher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static function(ExecuteChatScriptStepMessage $message): bool {
                return 7 === $message->executionId
                    && 10 === $message->tradeId
                    && 12 === $message->stepId
                    && 0 === $message->delaySeconds;
            }))
        ;

        $useCase = new ExecuteQueuedChatScriptStepUseCase(
            $executionRepository,
            $stepRepository,
            $tradeRepository,
            $messageRepository,
            $chatGateway,
            $publisher,
            new NullLogger(),
        );

        $useCase->execute(new ExecuteChatScriptStepMessage(executionId: 7, tradeId: 10, stepId: 11));
    }
}
