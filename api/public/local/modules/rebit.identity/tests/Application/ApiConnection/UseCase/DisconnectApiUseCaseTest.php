<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Application\ApiConnection\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Identity\Application\ApiConnection\UseCase\DisconnectApiUseCase;
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

        $repository = $this->createMock(ApiConnectionRepository::class);

        $repository
            ->expects($this->once())
            ->method('findActiveByUserId')
            ->with($userId)
            ->willReturn(['ID' => '10', 'UF_USER_ID' => $userId])
        ;

        $repository
            ->expects($this->once())
            ->method('revokeByUserId')
            ->with($userId)
        ;

        (new DisconnectApiUseCase($repository))->execute($userId);
    }

    public function testDisconnectWithNoActiveConnectionThrows404(): void
    {
        $repository = $this->createStub(ApiConnectionRepository::class);

        $repository
            ->method('findActiveByUserId')
            ->willReturn(false)
        ;

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Active API connection not found');
        $this->expectExceptionCode(404);

        (new DisconnectApiUseCase($repository))->execute(99);
    }

    public function testRevokeNotCalledWhenNoActiveConnection(): void
    {
        $repository = $this->createMock(ApiConnectionRepository::class);

        $repository
            ->method('findActiveByUserId')
            ->willReturn(false)
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
