<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Application\ApiConnection\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Bybit\Application\Shared\Dto\BybitResponseDto;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Bybit\Infrastructure\Exception\BybitApiException;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Application\ApiConnection\UseCase\VerifyApiUseCase;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class VerifyApiUseCaseTest extends TestCase
{
    private function createUseCase(
        ApiConnectionRepository $repository,
        ApiKeyEncryptor $encryptor,
        BybitClientInterface $bybitClient,
    ): VerifyApiUseCase {
        return new VerifyApiUseCase(
            repository: $repository,
            encryptor: $encryptor,
            masker: new ApiKeyMasker(),
            bybitClient: $bybitClient,
        );
    }

    public function testSuccessfulVerificationSetsActiveStatus(): void
    {
        $userId = 1;
        $connection = $this->buildConnection(10, $userId, 'testnet');

        $repository = $this->createMock(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);
        $bybitClient = $this->createStub(BybitClientInterface::class);

        $repository
            ->method('findActiveByUserId')
            ->with($userId)
            ->willReturn($connection)
        ;

        $encryptor
            ->method('decrypt')
            ->willReturnMap([
                ['encrypted_key', 'EO0gCaxwD79OuvUqxT'],
                ['encrypted_secret', 'my-secret-value1'],
            ])
        ;

        $bybitClient
            ->method('get')
            ->willReturn(new BybitResponseDto(0, 'OK', [], [], 0))
        ;

        $repository
            ->expects($this->once())
            ->method('update')
            ->with(10, $this->callback(function(array $fields): bool {
                return ConnectionStatusEnum::Active->value === $fields['UF_STATUS']
                    && $fields['UF_VERIFIED_AT'] instanceof DateTime;
            }))
        ;

        $result = $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute($userId)
        ;

        self::assertInstanceOf(ApiConnectionResultDto::class, $result);
        self::assertTrue($result->connected);
        self::assertSame(ConnectionStatusEnum::Active, $result->status);
        self::assertNotNull($result->verifiedAt);
    }

    public function testFailedVerificationSetsInvalidStatus(): void
    {
        $userId = 2;
        $connection = $this->buildConnection(20, $userId, 'mainnet');

        $repository = $this->createMock(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);
        $bybitClient = $this->createStub(BybitClientInterface::class);

        $repository
            ->method('findActiveByUserId')
            ->willReturn($connection)
        ;

        $encryptor->method('decrypt')->willReturn('decrypted-value1');

        $bybitClient
            ->method('get')
            ->willThrowException(new BybitApiException('Invalid key'))
        ;

        $repository
            ->expects($this->once())
            ->method('update')
            ->with(20, $this->callback(function(array $fields): bool {
                return ConnectionStatusEnum::Invalid->value === $fields['UF_STATUS'];
            }))
        ;

        $result = $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute($userId)
        ;

        self::assertSame(ConnectionStatusEnum::Invalid, $result->status);
    }

    public function testNoActiveConnectionThrows404(): void
    {
        $repository = $this->createStub(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);
        $bybitClient = $this->createStub(BybitClientInterface::class);

        $repository->method('findActiveByUserId')->willReturn(false);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);

        $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute(99)
        ;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildConnection(int $id, int $userId, string $mode): array
    {
        return [
            'ID' => (string)$id,
            'UF_USER_ID' => $userId,
            'UF_API_KEY_ENCRYPTED' => 'encrypted_key',
            'UF_SECRET_KEY_ENCRYPTED' => 'encrypted_secret',
            'UF_MODE' => $mode,
            'UF_STATUS' => ConnectionStatusEnum::Active->value,
            'UF_CREATED_AT' => new DateTime(),
            'UF_VERIFIED_AT' => null,
        ];
    }
}
