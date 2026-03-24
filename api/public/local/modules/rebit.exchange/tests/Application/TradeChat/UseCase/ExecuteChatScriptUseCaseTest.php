<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\ExecuteChatScriptUseCase;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class ExecuteChatScriptUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testSuccessfulScriptExecution(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');

        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(self::USER_ID);

        $step = $this->createStub(ChatScriptStep::class);
        $step->method('getId')->willReturn(1);
        $step->method('getUfMessage')->willReturn('Автоответ');
        $step->method('getUfDelaySeconds')->willReturn(0);

        $stepsCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepsCollection->method('getIterator')->willReturn(new \ArrayIterator([$step]));

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn($script);

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $stepRepo->method('findByScriptId')->willReturn($stepsCollection);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway
            ->expects($this->once())
            ->method('sendMessage')
        ;

        $msgRepo = $this->createMock(TradeMessageRepository::class);
        $msgRepo
            ->expects($this->once())
            ->method('create')
        ;

        $useCase = new ExecuteChatScriptUseCase(
            $scriptRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        $sentCount = $useCase->execute(1, 10, self::USER_ID);

        self::assertSame(1, $sentCount);
    }

    public function testStopsOnBybitError(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');

        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(self::USER_ID);

        $step1 = $this->createStub(ChatScriptStep::class);
        $step1->method('getId')->willReturn(1);
        $step1->method('getUfMessage')->willReturn('Шаг 1');
        $step1->method('getUfDelaySeconds')->willReturn(0);

        $step2 = $this->createStub(ChatScriptStep::class);
        $step2->method('getId')->willReturn(2);
        $step2->method('getUfMessage')->willReturn('Шаг 2');
        $step2->method('getUfDelaySeconds')->willReturn(0);

        $stepsCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepsCollection->method('getIterator')->willReturn(new \ArrayIterator([$step1, $step2]));

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn($script);

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $stepRepo->method('findByScriptId')->willReturn($stepsCollection);

        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);
        $chatGateway->method('sendMessage')
            ->willThrowException(new HttpException('Bybit error'))
        ;

        $msgRepo = $this->createStub(TradeMessageRepository::class);

        $useCase = new ExecuteChatScriptUseCase(
            $scriptRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        $sentCount = $useCase->execute(1, 10, self::USER_ID);

        self::assertSame(0, $sentCount);
    }

    public function testTradeNotFoundThrows404(): void
    {
        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn(null);

        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $msgRepo = $this->createStub(TradeMessageRepository::class);
        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);

        $useCase = new ExecuteChatScriptUseCase(
            $scriptRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Сделка не найдена');

        $useCase->execute(1, 10, self::USER_ID);
    }

    public function testScriptNotFoundThrows404(): void
    {
        $trade = $this->createStub(Trade::class);

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn(null);

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $msgRepo = $this->createStub(TradeMessageRepository::class);
        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);

        $useCase = new ExecuteChatScriptUseCase(
            $scriptRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Скрипт не найден');

        $useCase->execute(1, 10, self::USER_ID);
    }

    public function testAccessDeniedForNonOwnerThrows403(): void
    {
        $trade = $this->createStub(Trade::class);

        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(999);

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn($script);

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $msgRepo = $this->createStub(TradeMessageRepository::class);
        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);

        $useCase = new ExecuteChatScriptUseCase(
            $scriptRepo,
            $stepRepo,
            $tradeRepo,
            $msgRepo,
            $chatGateway,
            new NullLogger(),
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        $useCase->execute(1, 10, self::USER_ID);
    }
}
