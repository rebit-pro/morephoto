<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\ChatScript\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptListResultDto;
use Rebit\Exchange\Application\ChatScript\UseCase\ListChatScriptsUseCase;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;

/**
 * @internal
 */
final class ListChatScriptsUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testReturnsEmptyListWhenNoScripts(): void
    {
        $collection = $this->createStub(ChatScriptCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $scriptRepo = $this->createMock(ChatScriptRepository::class);
        $scriptRepo
            ->expects($this->once())
            ->method('findByUserId')
            ->with(self::USER_ID)
            ->willReturn($collection)
        ;

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);

        $result = (new ListChatScriptsUseCase($scriptRepo, $stepRepo))->execute(self::USER_ID);

        self::assertInstanceOf(ChatScriptListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithScriptsAndSteps(): void
    {
        $now = new DateTime();

        $script = $this->createStub(ChatScript::class);
        $script->method('getId')->willReturn(1);
        $script->method('getUfName')->willReturn('Приветствие');
        $script->method('getUfIsActive')->willReturn(1);
        $script->method('getUfCreatedAt')->willReturn($now);
        $script->method('getUfUpdatedAt')->willReturn($now);

        $scriptCollection = $this->createStub(ChatScriptCollection::class);
        $scriptCollection->method('getIterator')->willReturn(new \ArrayIterator([$script]));

        $step = $this->createStub(ChatScriptStep::class);
        $step->method('getId')->willReturn(10);
        $step->method('getUfSort')->willReturn(100);
        $step->method('getUfMessage')->willReturn('Привет!');
        $step->method('getUfDelaySeconds')->willReturn(5);

        $stepCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepCollection->method('getIterator')->willReturn(new \ArrayIterator([$step]));

        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findByUserId')->willReturn($scriptCollection);

        $stepRepo = $this->createMock(ChatScriptStepRepository::class);
        $stepRepo
            ->expects($this->once())
            ->method('findByScriptId')
            ->with(1)
            ->willReturn($stepCollection)
        ;

        $result = (new ListChatScriptsUseCase($scriptRepo, $stepRepo))->execute(self::USER_ID);

        self::assertCount(1, $result->items);

        $item = $result->items[0];
        self::assertSame(1, $item->id);
        self::assertSame('Приветствие', $item->name);
        self::assertTrue($item->isActive);
        self::assertCount(1, $item->steps);
        self::assertSame(10, $item->steps[0]->id);
        self::assertSame(100, $item->steps[0]->sort);
        self::assertSame('Привет!', $item->steps[0]->message);
        self::assertSame(5, $item->steps[0]->delaySeconds);
    }

    public function testReturnsMultipleScriptsWithEmptySteps(): void
    {
        $now = new DateTime();

        $script1 = $this->createStub(ChatScript::class);
        $script1->method('getId')->willReturn(1);
        $script1->method('getUfName')->willReturn('Скрипт 1');
        $script1->method('getUfIsActive')->willReturn(1);
        $script1->method('getUfCreatedAt')->willReturn($now);
        $script1->method('getUfUpdatedAt')->willReturn($now);

        $script2 = $this->createStub(ChatScript::class);
        $script2->method('getId')->willReturn(2);
        $script2->method('getUfName')->willReturn('Скрипт 2');
        $script2->method('getUfIsActive')->willReturn(0);
        $script2->method('getUfCreatedAt')->willReturn($now);
        $script2->method('getUfUpdatedAt')->willReturn(null);

        $scriptCollection = $this->createStub(ChatScriptCollection::class);
        $scriptCollection->method('getIterator')->willReturn(new \ArrayIterator([$script1, $script2]));

        $emptyStepCollection = $this->createStub(ChatScriptStepCollection::class);
        $emptyStepCollection->method('getIterator')->willReturn(new \ArrayIterator([]));

        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findByUserId')->willReturn($scriptCollection);

        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $stepRepo->method('findByScriptId')->willReturn($emptyStepCollection);

        $result = (new ListChatScriptsUseCase($scriptRepo, $stepRepo))->execute(self::USER_ID);

        self::assertCount(2, $result->items);
        self::assertSame('Скрипт 1', $result->items[0]->name);
        self::assertTrue($result->items[0]->isActive);
        self::assertSame([], $result->items[0]->steps);

        self::assertSame('Скрипт 2', $result->items[1]->name);
        self::assertFalse($result->items[1]->isActive);
        self::assertNull($result->items[1]->updatedAt);
    }
}
