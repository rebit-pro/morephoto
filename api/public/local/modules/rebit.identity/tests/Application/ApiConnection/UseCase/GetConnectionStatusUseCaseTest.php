<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Application\ApiConnection\UseCase\GetConnectionStatusUseCase;
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

        $repository = $this->createMock(ApiConnectionRepository::class);
        $encryptor = $this->createMock(ApiKeyEncryptor::class);

        $repository
            ->expects($this->once())
            ->method('findByUserId')
            ->with($userId)
            ->willReturn([
                'ID' => '5',
                'UF_USER_ID' => $userId,
                'UF_API_KEY_ENCRYPTED' => 'enc_key',
                'UF_STATUS' => 'active',
                'UF_MODE' => 'testnet',
                'UF_CREATED_AT' => $createdAt,
                'UF_VERIFIED_AT' => $createdAt,
            ])
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

        $repository->method('findByUserId')->willReturn(false);

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
        $repository = $this->createStub(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);

        $repository
            ->method('findByUserId')
            ->willReturn([
                'ID' => '1',
                'UF_USER_ID' => 1,
                'UF_API_KEY_ENCRYPTED' => 'enc',
                'UF_STATUS' => 'invalid',
                'UF_MODE' => 'mainnet',
                'UF_CREATED_AT' => new DateTime(),
                'UF_VERIFIED_AT' => null,
            ])
        ;

        $encryptor->method('decrypt')->willReturn('1234567890123456');

        $result = $this->createUseCase($repository, $encryptor)->execute(1);

        self::assertTrue($result->connected);
        self::assertNull($result->verifiedAt);
        self::assertSame(ConnectionStatusEnum::Invalid, $result->status);
    }
}
