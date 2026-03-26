<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Application\ApiConnection\UseCase\GetConnectionStatusUseCase;
use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;

/**
 * @internal
 */
final class GetConnectionStatusUseCaseTest extends TestCase
{
    private function createUseCase(
        ApiConnectionRepository $repository,
        ApiKeyEncryptor $encryptor,
    ): GetConnectionStatusUseCase {
        return new GetConnectionStatusUseCase(
            repository: $repository,
            encryptor: $encryptor,
            masker: new ApiKeyMasker(),
        );
    }

    public function testReturnsConnectionStatus(): void
    {
        $userId = 1;
        $createdAt = new DateTime();

        $connection = $this->createStub(ApiConnection::class);
        $connection->method('getId')->willReturn(5);
        $connection->method('getUfApiKeyEncrypted')->willReturn('enc_key');
        $connection->method('getUfStatus')->willReturn('active');
        $connection->method('getUfMode')->willReturn('testnet');
        $connection->method('getUfCreatedAt')->willReturn($createdAt);
        $connection->method('getUfLastVerifiedAt')->willReturn($createdAt);

        $repository = $this->createMock(ApiConnectionRepository::class);
        $encryptor = $this->createMock(ApiKeyEncryptor::class);

        $repository
            ->expects($this->once())
            ->method('findByUserId')
            ->with($userId)
            ->willReturn($connection)
        ;

        $encryptor
            ->expects($this->once())
            ->method('decrypt')
            ->with('enc_key')
            ->willReturn('EO0gCaxwD79OuvUqxT')
        ;

        $result = $this->createUseCase($repository, $encryptor)->execute($userId);

        self::assertInstanceOf(ApiConnectionResultDto::class, $result);
        self::assertTrue($result->connected);
        self::assertSame(5, $result->id);
        self::assertSame($userId, $result->userId);
        self::assertSame(ConnectionStatusEnum::Active, $result->status);
        self::assertSame(ConnectionModeEnum::Testnet, $result->mode);
        self::assertSame('EO0g**********UqxT', $result->maskedApiKey);
        self::assertNotEmpty($result->createdAt);
        self::assertNotNull($result->verifiedAt);
    }

    public function testNoConnectionReturnsDisconnected(): void
    {
        $repository = $this->createStub(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);

        $repository->method('findByUserId')->willReturn(null);

        $result = $this->createUseCase($repository, $encryptor)->execute(999);

        self::assertInstanceOf(ApiConnectionResultDto::class, $result);
        self::assertFalse($result->connected);
        self::assertNull($result->status);
        self::assertNull($result->mode);
        self::assertNull($result->id);
        self::assertNull($result->maskedApiKey);
    }

    public function testNullVerifiedAtReturnsNullInResult(): void
    {
        $connection = $this->createStub(ApiConnection::class);
        $connection->method('getId')->willReturn(1);
        $connection->method('getUfApiKeyEncrypted')->willReturn('enc');
        $connection->method('getUfStatus')->willReturn('invalid');
        $connection->method('getUfMode')->willReturn('mainnet');
        $connection->method('getUfCreatedAt')->willReturn(new DateTime());
        $connection->method('getUfLastVerifiedAt')->willReturn(null);

        $repository = $this->createStub(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);

        $repository->method('findByUserId')->willReturn($connection);
        $encryptor->method('decrypt')->willReturn('1234567890123456');

        $result = $this->createUseCase($repository, $encryptor)->execute(1);

        self::assertTrue($result->connected);
        self::assertNull($result->verifiedAt);
        self::assertSame(ConnectionStatusEnum::Invalid, $result->status);
    }

    public function testRevokedConnectionReturnsDisconnected(): void
    {
        $connection = $this->createStub(ApiConnection::class);
        $connection->method('getUfStatus')->willReturn('revoked');

        $repository = $this->createStub(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);

        $repository->method('findByUserId')->willReturn($connection);

        $result = $this->createUseCase($repository, $encryptor)->execute(1);

        self::assertFalse($result->connected);
        self::assertNull($result->status);
        self::assertNull($result->id);
    }
}
