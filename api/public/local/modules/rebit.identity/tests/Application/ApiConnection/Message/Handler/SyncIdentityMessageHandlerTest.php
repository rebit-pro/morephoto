<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Application\ApiConnection\Message\Handler;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Application\ApiConnection\Message\Handler\SyncIdentityMessageHandler;
use Rebit\Identity\Application\ApiConnection\Message\SyncIdentityMessage;
use Rebit\Identity\Application\ApiConnection\UseCase\VerifyApiUseCase;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class SyncIdentityMessageHandlerTest extends TestCase
{
    public function testHandlesSuccessfulSync(): void
    {
        $result = new ApiConnectionResultDto(
            connected: true,
            status: ConnectionStatusEnum::Active,
            verifiedAt: '2026-03-28T20:00:00+00:00',
        );

        $useCase = $this->createStub(VerifyApiUseCase::class);
        $useCase->method('execute')->willReturn($result);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('info');

        $handler = new SyncIdentityMessageHandler($useCase, $logger);
        $handler(new SyncIdentityMessage(userId: 10));
    }

    public function testLogsWarningAndReturnsOn404(): void
    {
        $useCase = $this->createStub(VerifyApiUseCase::class);
        $useCase->method('execute')->willThrowException(new HttpException('Not found', 404));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $handler = new SyncIdentityMessageHandler($useCase, $logger);
        $handler(new SyncIdentityMessage(userId: 10));
    }

    public function testRethrowsNon404HttpException(): void
    {
        $useCase = $this->createStub(VerifyApiUseCase::class);
        $useCase->method('execute')->willThrowException(new HttpException('Boom', 500));

        $logger = $this->createStub(LoggerInterface::class);
        $handler = new SyncIdentityMessageHandler($useCase, $logger);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(500);

        $handler(new SyncIdentityMessage(userId: 10));
    }
}
