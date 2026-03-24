<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\ChatScript\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\ChatScript\UseCase\DeleteChatScriptUseCase;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class DeleteChatScriptUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testSuccessfulDeletion(): void
    {
        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(self::USER_ID);
        $scriptRepo = $this->createMock(ChatScriptRepository::class);
        $scriptRepo->method('findById')->with(1)->willReturn($script);
        $scriptRepo->expects($this->once())->method('delete')->with(1);
        $stepRepo = $this->createMock(ChatScriptStepRepository::class);
        $stepRepo->expects($this->once())->method('deleteByScriptId')->with(1);
        $adRepo = $this->createMock(AdvertisementRepository::class);
        $adRepo->expects($this->once())->method('clearChatScriptId')->with(1);
        (new DeleteChatScriptUseCase($scriptRepo, $stepRepo, $adRepo))->execute(1, self::USER_ID);
    }

    public function testScriptNotFoundThrows404(): void
    {
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn(null);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $adRepo = $this->createStub(AdvertisementRepository::class);
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Скрипт не найден');
        (new DeleteChatScriptUseCase($scriptRepo, $stepRepo, $adRepo))->execute(999, self::USER_ID);
    }

    public function testAccessDeniedThrows403(): void
    {
        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(999);
        $scriptRepo = $this->createStub(ChatScriptRepository::class);
        $scriptRepo->method('findById')->willReturn($script);
        $stepRepo = $this->createStub(ChatScriptStepRepository::class);
        $adRepo = $this->createStub(AdvertisementRepository::class);
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);
        (new DeleteChatScriptUseCase($scriptRepo, $stepRepo, $adRepo))->execute(1, self::USER_ID);
    }

    public function testClearsAdvertisementChatScriptBeforeDeletion(): void
    {
        $script = $this->createStub(ChatScript::class);
        $script->method('getUfUserId')->willReturn(self::USER_ID);
        $callOrder = [];
        $adRepo = $this->createMock(AdvertisementRepository::class);
        $adRepo
            ->expects($this->once())
            ->method('clearChatScriptId')
            ->with(5)
            ->willReturnCallback(function() use (&$callOrder): void {
                $callOrder[] = 'clearChatScript';
            })
        ;
        $stepRepo = $this->createMock(ChatScriptStepRepository::class);
        $stepRepo
            ->expects($this->once())
            ->method('deleteByScriptId')
            ->with(5)
            ->willReturnCallback(function() use (&$callOrder): void {
                $callOrder[] = 'deleteSteps';
            })
        ;
        $scriptRepo = $this->createMock(ChatScriptRepository::class);
        $scriptRepo->method('findById')->with(5)->willReturn($script);
        $scriptRepo
            ->expects($this->once())
            ->method('delete')
            ->with(5)
            ->willReturnCallback(function() use (&$callOrder): void {
                $callOrder[] = 'deleteScript';
            })
        ;
        (new DeleteChatScriptUseCase($scriptRepo, $stepRepo, $adRepo))->execute(5, self::USER_ID);
        self::assertSame(['clearChatScript', 'deleteSteps', 'deleteScript'], $callOrder);
    }
}
