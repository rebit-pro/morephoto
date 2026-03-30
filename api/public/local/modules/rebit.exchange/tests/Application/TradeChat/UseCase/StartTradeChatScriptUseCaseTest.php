<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;
use Rebit\Exchange\Application\TradeChat\Port\ChatScriptStepPublisherInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\StartTradeChatScriptUseCase;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecution;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;

/**
 * @internal
 */
final class StartTradeChatScriptUseCaseTest extends TestCase
{
    public function testDispatchesImmediateFirstStep(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(10);
        $trade->method('getUfAdvertisementId')->willReturn(20);

        $advertisement = $this->createStub(Advertisement::class);
        $advertisement->method('getId')->willReturn(20);
        $advertisement->method('getUfChatScriptId')->willReturn(30);
        $advertisement->method('getUfUserId')->willReturn(42);

        $script = $this->createStub(ChatScript::class);
        $script->method('getId')->willReturn(30);
        $script->method('getUfIsActive')->willReturn(1);

        $firstStep = $this->createStub(ChatScriptStep::class);
        $firstStep->method('getId')->willReturn(50);
        $firstStep->method('getUfDelaySeconds')->willReturn(0);

        $steps = new ChatScriptStepCollection();
        $steps[] = $firstStep;

        $execution = $this->createStub(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(70);

        $advertisementRepository = $this->createStub(AdvertisementRepository::class);
        $advertisementRepository->method('findById')->willReturn($advertisement);

        $chatScriptRepository = $this->createStub(ChatScriptRepository::class);
        $chatScriptRepository->method('findById')->willReturn($script);

        $chatScriptStepRepository = $this->createStub(ChatScriptStepRepository::class);
        $chatScriptStepRepository->method('findByScriptId')->willReturn($steps);

        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);
        $executionRepository->method('existsPendingForTrade')->willReturn(false);
        $executionRepository->method('enqueue')->willReturn($execution);

        $publisher = $this->createMock(ChatScriptStepPublisherInterface::class);
        $publisher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(static function(ExecuteChatScriptStepMessage $message): bool {
                return 70 === $message->executionId
                    && 10 === $message->tradeId
                    && 50 === $message->stepId
                    && 0 === $message->delaySeconds;
            }))
        ;

        $useCase = new StartTradeChatScriptUseCase(
            $advertisementRepository,
            $chatScriptRepository,
            $chatScriptStepRepository,
            $executionRepository,
            $publisher,
            new NullLogger(),
        );

        $useCase->execute($trade);
    }

    public function testDoesNotDispatchDelayedFirstStep(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(10);
        $trade->method('getUfAdvertisementId')->willReturn(20);

        $advertisement = $this->createStub(Advertisement::class);
        $advertisement->method('getId')->willReturn(20);
        $advertisement->method('getUfChatScriptId')->willReturn(30);
        $advertisement->method('getUfUserId')->willReturn(42);

        $script = $this->createStub(ChatScript::class);
        $script->method('getId')->willReturn(30);
        $script->method('getUfIsActive')->willReturn(1);

        $firstStep = $this->createStub(ChatScriptStep::class);
        $firstStep->method('getId')->willReturn(50);
        $firstStep->method('getUfDelaySeconds')->willReturn(120);

        $steps = new ChatScriptStepCollection();
        $steps[] = $firstStep;

        $execution = $this->createStub(ChatScriptExecution::class);
        $execution->method('getId')->willReturn(70);

        $advertisementRepository = $this->createStub(AdvertisementRepository::class);
        $advertisementRepository->method('findById')->willReturn($advertisement);

        $chatScriptRepository = $this->createStub(ChatScriptRepository::class);
        $chatScriptRepository->method('findById')->willReturn($script);

        $chatScriptStepRepository = $this->createStub(ChatScriptStepRepository::class);
        $chatScriptStepRepository->method('findByScriptId')->willReturn($steps);

        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);
        $executionRepository->method('existsPendingForTrade')->willReturn(false);
        $executionRepository->method('enqueue')->willReturn($execution);

        $publisher = $this->createMock(ChatScriptStepPublisherInterface::class);
        $publisher->expects($this->never())->method('dispatch');

        $useCase = new StartTradeChatScriptUseCase(
            $advertisementRepository,
            $chatScriptRepository,
            $chatScriptStepRepository,
            $executionRepository,
            $publisher,
            new NullLogger(),
        );

        $useCase->execute($trade);
    }

    public function testSkipsWhenPendingExecutionAlreadyExists(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(10);
        $trade->method('getUfAdvertisementId')->willReturn(20);

        $advertisement = $this->createStub(Advertisement::class);
        $advertisement->method('getId')->willReturn(20);
        $advertisement->method('getUfChatScriptId')->willReturn(30);

        $script = $this->createStub(ChatScript::class);
        $script->method('getUfIsActive')->willReturn(1);

        $advertisementRepository = $this->createStub(AdvertisementRepository::class);
        $advertisementRepository->method('findById')->willReturn($advertisement);

        $chatScriptRepository = $this->createStub(ChatScriptRepository::class);
        $chatScriptRepository->method('findById')->willReturn($script);

        $chatScriptStepRepository = $this->createStub(ChatScriptStepRepository::class);

        $executionRepository = $this->createStub(ChatScriptExecutionRepository::class);
        $executionRepository->method('existsPendingForTrade')->willReturn(true);

        $publisher = $this->createMock(ChatScriptStepPublisherInterface::class);
        $publisher->expects($this->never())->method('dispatch');

        $useCase = new StartTradeChatScriptUseCase(
            $advertisementRepository,
            $chatScriptRepository,
            $chatScriptStepRepository,
            $executionRepository,
            $publisher,
            new NullLogger(),
        );

        $useCase->execute($trade);
    }
}
