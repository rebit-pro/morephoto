<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\ChatScript\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\ChatScript\Dto\Request\CreateChatScriptRequestDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptResultDto;
use Rebit\Exchange\Application\ChatScript\UseCase\CreateChatScriptUseCase;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Share\Shared\Exception\ValidationHttpException;

/**
 * @internal
 */
final class CreateChatScriptUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testSuccessfulCreation(): void
    {
        $dto = new CreateChatScriptRequestDto(
            name: 'Мой скрипт',
            isActive: true,
            steps: [
                ['sort' => 100, 'message' => 'Привет!', 'delaySeconds' => 5],
            ],
        );
        $now = new DateTime();
        $script = $this->createStub(ChatScript::class);
        $script->method('getId')->willReturn(1);
        $script->method('getUfName')->willReturn('Мой скрипт');
        $script->method('getUfIsActive')->willReturn(1);
        $script->method('getUfCreatedAt')->willReturn($now);
        $script->method('getUfUpdatedAt')->willReturn($now);
        $step = $this->createStub(ChatScriptStep::class);
        $step->method('getId')->willReturn(10);
        $step->method('getUfSort')->willReturn(100);
        $step->method('getUfMessage')->willReturn('Привет!');
        $step->method('getUfDelaySeconds')->willReturn(5);
        $stepCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepCollection->method('getIterator')->willReturn(new \ArrayIterator([$step]));
        $scriptRepo = $this->createMock(ChatScriptRepository::class);
        $scriptRepo
            ->expects($this->once())
            ->method('create')
            ->with(self::USER_ID, 'Мой скрипт', true)
            ->willReturn($script)
        ;
        $stepRepo = $this->createMock(ChatScriptStepRepository::class);
        $stepRepo
            ->expects($this->once())
            ->method('replaceSteps')
            ->with(1, $dto->steps)
            ->willReturn($stepCollection)
        ;
        $result = (new CreateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
        self::assertInstanceOf(ChatScriptResultDto::class, $result);
        self::assertSame(1, $result->id);
        self::assertSame('Мой скрипт', $result->name);
        self::assertTrue($result->isActive);
        self::assertCount(1, $result->steps);
        self::assertSame(10, $result->steps[0]->id);
    }

    public function testEmptyNameThrowsValidation(): void
    {
        $dto = new CreateChatScriptRequestDto(
            name: '   ',
            isActive: true,
            steps: [
                ['sort' => 100, 'message' => 'Привет!', 'delaySeconds' => 5],
            ],
        );
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $this->expectException(ValidationHttpException::class);
        $this->expectExceptionMessage('Название скрипта не может быть пустым');
        (new CreateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
    }

    public function testEmptyStepsThrowsValidation(): void
    {
        $dto = new CreateChatScriptRequestDto(
            name: 'Мой скрипт',
            isActive: true,
            steps: [],
        );
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $this->expectException(ValidationHttpException::class);
        $this->expectExceptionMessage('Скрипт должен содержать хотя бы один шаг');
        (new CreateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
    }

    public function testInactiveScriptCreation(): void
    {
        $dto = new CreateChatScriptRequestDto(
            name: 'Неактивный',
            isActive: false,
            steps: [
                ['sort' => 100, 'message' => 'Тест', 'delaySeconds' => 0],
            ],
        );
        $now = new DateTime();
        $script = $this->createStub(ChatScript::class);
        $script->method('getId')->willReturn(2);
        $script->method('getUfName')->willReturn('Неактивный');
        $script->method('getUfIsActive')->willReturn(0);
        $script->method('getUfCreatedAt')->willReturn($now);
        $script->method('getUfUpdatedAt')->willReturn($now);
        $stepCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepCollection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $scriptRepo = $this->createMock(ChatScriptRepository::class);
        $scriptRepo
            ->expects($this->once())
            ->method('create')
            ->with(self::USER_ID, 'Неактивный', false)
            ->willReturn($script)
        ;
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $stepRepo->method('replaceSteps')->willReturn($stepCollection);
        $result = (new CreateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
        self::assertFalse($result->isActive);
    }
}
