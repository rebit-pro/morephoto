<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\ChatScript\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\ChatScript\Dto\Request\UpdateChatScriptRequestDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptResultDto;
use Rebit\Exchange\Application\ChatScript\UseCase\UpdateChatScriptUseCase;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Share\Shared\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\ValidationHttpException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class UpdateChatScriptUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testSuccessfulUpdate(): void
    {
        $dto = new UpdateChatScriptRequestDto(
            id: 1,
            name: 'Обновлённый',
            isActive: true,
            steps: [
                ['sort' => 100, 'message' => 'Новое сообщение', 'delaySeconds' => 10],
            ],
        );
        $now = new DateTime();
        $script = $this->createMock(ChatScript::class);
        $script->method('getId')->willReturn(1);
        $script->method('getUfUserId')->willReturn(self::USER_ID);
        $script->method('getUfName')->willReturn('Обновлённый');
        $script->method('getUfIsActive')->willReturn(1);
        $script->method('getUfCreatedAt')->willReturn($now);
        $script->method('getUfUpdatedAt')->willReturn($now);
        $script->expects($this->once())->method('setUfName')->with('Обновлённый')->willReturnSelf();
        $script->expects($this->once())->method('setUfIsActive')->with(1)->willReturnSelf();
        $step = $this->createStub(ChatScriptStep::class);
        $step->method('getId')->willReturn(20);
        $step->method('getUfSort')->willReturn(100);
        $step->method('getUfMessage')->willReturn('Новое сообщение');
        $step->method('getUfDelaySeconds')->willReturn(10);
        $stepCollection = $this->createStub(ChatScriptStepCollection::class);
        $stepCollection->method('getIterator')->willReturn(new \ArrayIterator([$step]));
        $scriptRepo = $this->createMock(ChatScriptRepository::class);
        $scriptRepo->method('findById')->with(1)->willReturn($script);
        $scriptRepo->expects($this->once())->method('save')->with($script);
        $stepRepo = $this->createMock(ChatScriptStepRepository::class);
        $stepRepo
            ->expects($this->once())
            ->method('replaceSteps')
            ->with(1, $dto->steps)
            ->willReturn($stepCollection)
        ;
        $result = (new UpdateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
        self::assertInstanceOf(ChatScriptResultDto::class, $result);
        self::assertSame(1, $result->id);
        self::assertSame('Обновлённый', $result->name);
        self::assertCount(1, $result->steps);
    }

    public function testScriptNotFoundThrows404(): void
    {
        $dto = new UpdateChatScriptRequestDto(
            id: 999,
            name: 'Тест',
            isActive: true,
            steps: [
                ['sort' => 100, 'message' => 'Тест', 'delaySeconds' => 0],
            ],
        );
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn(null);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Скрипт не найден');
        (new UpdateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
    }

    public function testAccessDeniedThrows403(): void
    {
        $dto = new UpdateChatScriptRequestDto(
            id: 1,
            name: 'Тест',
            isActive: true,
            steps: [
                ['sort' => 100, 'message' => 'Тест', 'delaySeconds' => 0],
            ],
        );
        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(999);
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn($script);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);
        (new UpdateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
    }

    public function testEmptyNameThrowsValidation(): void
    {
        $dto = new UpdateChatScriptRequestDto(
            id: 1,
            name: '',
            isActive: true,
            steps: [
                ['sort' => 100, 'message' => 'Тест', 'delaySeconds' => 0],
            ],
        );
        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(self::USER_ID);
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn($script);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $this->expectException(ValidationHttpException::class);
        $this->expectExceptionMessage('Название скрипта не может быть пустым');
        (new UpdateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
    }

    public function testEmptyStepsThrowsValidation(): void
    {
        $dto = new UpdateChatScriptRequestDto(
            id: 1,
            name: 'Тест',
            isActive: true,
            steps: [],
        );
        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(self::USER_ID);
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn($script);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $this->expectException(ValidationHttpException::class);
        $this->expectExceptionMessage('Скрипт должен содержать хотя бы один шаг');
        (new UpdateChatScriptUseCase($scriptRepo, $stepRepo))->execute($dto, self::USER_ID);
    }
}
