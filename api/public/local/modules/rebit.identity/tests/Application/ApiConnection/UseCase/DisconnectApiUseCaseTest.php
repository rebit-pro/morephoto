<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Application\ApiConnection\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Identity\Application\ApiConnection\UseCase\DisconnectApiUseCase;
use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class DisconnectApiUseCaseTest extends TestCase
{
    public function testSuccessfulDisconnect(): void
    {
        $userId = 42;

        $connection = $this->createStub(ApiConnection::class);

        $repository = $this->createMock(ApiConnectionRepository::class);

        $repository
            ->expects($this->once())
            ->method('findNonRevokedByUserId')
            ->with($userId)
            ->willReturn($connection)
        ;

        $repository
            ->expects($this->once())
            ->method('revokeByUserId')
            ->with($userId)
        ;

        (new DisconnectApiUseCase($repository))->execute($userId);
    }

    public function testDisconnectWithNoConnectionThrows404(): void
    {
        $repository = $this->createStub(ApiConnectionRepository::class);

        $repository
            ->method('findNonRevokedByUserId')
            ->willReturn(null)
        ;

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('API connection not found');
        $this->expectExceptionCode(404);

        (new DisconnectApiUseCase($repository))->execute(99);
    }

    public function testRevokeNotCalledWhenNoConnection(): void
    {
        $repository = $this->createMock(ApiConnectionRepository::class);

        $repository
            ->method('findNonRevokedByUserId')
            ->willReturn(null)
        ;

        $repository
            ->expects($this->never())
            ->method('revokeByUserId')
        ;

        try {
            (new DisconnectApiUseCase($repository))->execute(1);
        } catch (HttpException) {
            // expected
        }
    }
}
