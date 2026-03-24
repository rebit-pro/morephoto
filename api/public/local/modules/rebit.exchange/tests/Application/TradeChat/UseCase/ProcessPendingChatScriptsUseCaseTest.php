<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\ProcessPendingChatScriptsUseCase;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecution;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecutionCollection;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Enum\ExecutionStatusEnum;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;

/**
 * @internal
 */
final class ProcessPendingChatScriptsUseCaseTest extends TestCase
{
    public function testSendsNextStepAndUpdatesExecution(): void
    {
        $execution = $this->createMock(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(1);
        $execution->method('getUfTradeId')->willReturn(10);
        $execution->method('getUfScriptId')->willReturn(20);
        $execution->method('getUfUserId')->willReturn(42);
        $execution->method('getUfLastStepSort')->willReturn(0);
        $execution->expects($this->once())->method('setUfLastStepSort')->with(100);
        $execution->expects($this->once())->method('setUfStatus')->with(ExecutionStatusEnum::Completed->value);

        $execCollection = $this->createStub(ChatScriptExecutionCollection::class);
        $execCollection->method('getIterator')->willReturn(new \ArrayIterator([$execution]));

        $execRepo = $this->createMock(ChatScriptExecutionRepository::class);
        $execRepo->method('findReadyToProcess')->willReturn($execCollection);
        $execRepo->expects($this->once())->method('save');

        $step = $this->createStub(ChatScriptStep::class);
        $step->method('getId')->willReturn(5);
        $step->method('getUfSort')->willReturn(100);
        $step->method('getUfMessage')->willReturn('Привет!');
        $step->method('getUfDelaySeconds')->willReturn(0);

        $stepsCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepsCollection->method('getIterator')->willReturn(new \ArrayIterator([$step]));

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $stepRepo->method('findByScriptId')->willReturn($stepsCollection);

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');
        $trade->method('getUfStatus')->willReturn('pending_payment');

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->once())->method('sendMessage');

        $msgRepo = $this->createMock(TradeMessageRepository::class);
        $msgRepo->expects($this->once())->method('create');

        $useCase = new ProcessPendingChatScriptsUseCase(
            $execRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        [$sent, $completed, $errors] = $useCase->execute();

        self::assertSame(1, $sent);
        self::assertSame(0, $completed);
        self::assertSame(0, $errors);
    }

    public function testCancelsExecutionWhenTradeNotFound(): void
    {
        $execution = $this->createMock(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(1);
        $execution->method('getUfTradeId')->willReturn(10);
        $execution->expects($this->once())->method('setUfStatus')->with(ExecutionStatusEnum::Cancelled->value);

        $execCollection = $this->createStub(ChatScriptExecutionCollection::class);
        $execCollection->method('getIterator')->willReturn(new \ArrayIterator([$execution]));

        $execRepo = $this->createMock(ChatScriptExecutionRepository::class);
        $execRepo->method('findReadyToProcess')->willReturn($execCollection);
        $execRepo->expects($this->once())->method('save');

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn(null);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->never())->method('sendMessage');

        $msgRepo = $this->createStub(TradeMessageRepository::class);

        $useCase = new ProcessPendingChatScriptsUseCase(
            $execRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        [$sent, $completed, $errors] = $useCase->execute();

        self::assertSame(0, $sent);
        self::assertSame(1, $completed);
        self::assertSame(0, $errors);
    }

    public function testCancelsExecutionWhenTradeChatNotActive(): void
    {
        $execution = $this->createMock(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(1);
        $execution->method('getUfTradeId')->willReturn(10);
        $execution->expects($this->once())->method('setUfStatus')->with(ExecutionStatusEnum::Cancelled->value);

        $execCollection = $this->createStub(ChatScriptExecutionCollection::class);
        $execCollection->method('getIterator')->willReturn(new \ArrayIterator([$execution]));

        $execRepo = $this->createMock(ChatScriptExecutionRepository::class);
        $execRepo->method('findReadyToProcess')->willReturn($execCollection);

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfStatus')->willReturn('completed');

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->never())->method('sendMessage');

        $msgRepo = $this->createStub(TradeMessageRepository::class);

        $useCase = new ProcessPendingChatScriptsUseCase(
            $execRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        [$sent, $completed] = $useCase->execute();

        self::assertSame(0, $sent);
        self::assertSame(1, $completed);
    }

    public function testCompletesWhenNoMoreSteps(): void
    {
        $execution = $this->createMock(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(1);
        $execution->method('getUfTradeId')->willReturn(10);
        $execution->method('getUfScriptId')->willReturn(20);
        $execution->method('getUfLastStepSort')->willReturn(999);
        $execution->expects($this->once())->method('setUfStatus')->with(ExecutionStatusEnum::Completed->value);

        $execCollection = $this->createStub(ChatScriptExecutionCollection::class);
        $execCollection->method('getIterator')->willReturn(new \ArrayIterator([$execution]));

        $execRepo = $this->createMock(ChatScriptExecutionRepository::class);
        $execRepo->method('findReadyToProcess')->willReturn($execCollection);

        $stepsCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepsCollection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $stepRepo->method('findByScriptId')->willReturn($stepsCollection);

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-1');
        $trade->method('getUfStatus')->willReturn('pending_payment');

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->never())->method('sendMessage');

        $msgRepo = $this->createStub(TradeMessageRepository::class);

        $useCase = new ProcessPendingChatScriptsUseCase(
            $execRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        [$sent, $completed] = $useCase->execute();

        self::assertSame(0, $sent);
        self::assertSame(1, $completed);
    }

    public function testReturnsZerosWhenQueueEmpty(): void
    {
        $execCollection = $this->createStub(ChatScriptExecutionCollection::class);
        $execCollection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $execRepo = $this->createStub(ChatScriptExecutionRepository::class);
        $execRepo->method('findReadyToProcess')->willReturn($execCollection);

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $tradeRepo = $this->createStub(TradeRepository::class);
        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);
        $msgRepo = $this->createStub(TradeMessageRepository::class);

        $useCase = new ProcessPendingChatScriptsUseCase(
            $execRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        [$sent, $completed, $errors] = $useCase->execute();

        self::assertSame(0, $sent);
        self::assertSame(0, $completed);
        self::assertSame(0, $errors);
    }

    public function testCountsErrorsWhenExceptionThrown(): void
    {
        $execution = $this->createStub(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(1);
        $execution->method('getUfTradeId')->willReturn(10);
        $execution->method('getUfScriptId')->willReturn(20);
        $execution->method('getUfUserId')->willReturn(42);
        $execution->method('getUfLastStepSort')->willReturn(0);

        $execCollection = $this->createStub(ChatScriptExecutionCollection::class);
        $execCollection->method('getIterator')->willReturn(new \ArrayIterator([$execution]));

        $execRepo = $this->createStub(ChatScriptExecutionRepository::class);
        $execRepo->method('findReadyToProcess')->willReturn($execCollection);

        $step = $this->createStub(ChatScriptStep::class);
        $step->method('getUfSort')->willReturn(100);
        $step->method('getUfMessage')->willReturn('test');
        $step->method('getUfDelaySeconds')->willReturn(0);

        $stepsCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepsCollection->method('getIterator')->willReturn(new \ArrayIterator([$step]));

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $stepRepo->method('findByScriptId')->willReturn($stepsCollection);

        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-1');
        $trade->method('getUfStatus')->willReturn('pending_payment');

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);
        $chatGateway->method('sendMessage')->willThrowException(new \RuntimeException('API down'));

        $msgRepo = $this->createStub(TradeMessageRepository::class);

        $useCase = new ProcessPendingChatScriptsUseCase(
            $execRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        [$sent, $completed, $errors] = $useCase->execute();

        self::assertSame(0, $sent);
        self::assertSame(0, $completed);
        self::assertSame(1, $errors);
    }
}
