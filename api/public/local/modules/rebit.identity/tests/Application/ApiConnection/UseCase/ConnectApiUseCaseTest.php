<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Application\ApiConnection\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Bybit\Application\Shared\Dto\BybitCredentialsDto;
use Rebit\Bybit\Application\Shared\Dto\BybitResponseDto;
use Rebit\Bybit\Application\Shared\Port\Outgoing\BybitClientInterface;
use Rebit\Bybit\Infrastructure\Exception\BybitApiException;
use Rebit\Bybit\Shared\Enum\BybitEnvironmentEnum;
use Rebit\Identity\Application\ApiConnection\Dto\Request\ConnectApiRequestDto;
use Rebit\Identity\Application\ApiConnection\Dto\Result\ApiConnectionResultDto;
use Rebit\Identity\Application\ApiConnection\UseCase\ConnectApiUseCase;
use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Identity\Domain\ApiConnection\Repository\ApiConnectionRepository;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;
use Bitrix\Main\Type\DateTime;

/**
 * @internal
 */
final class ConnectApiUseCaseTest extends TestCase
{
    private ApiKeyMasker $masker;

    protected function setUp(): void
    {
        $this->masker = new ApiKeyMasker();
    }

    private function createUseCase(
        ApiConnectionRepository $repository,
        ApiKeyEncryptor $encryptor,
        BybitClientInterface $bybitClient,
    ): ConnectApiUseCase {
        return new ConnectApiUseCase(
            repository: $repository,
            encryptor: $encryptor,
            masker: $this->masker,
            bybitClient: $bybitClient,
        );
    }

    public function testSuccessfulConnectionReturnsActiveStatus(): void
    {
        $userId = 1;
        $dto = new ConnectApiRequestDto(
            apiKey: 'EO0gCaxwD79OuvUqxT',
            secretKey: 'x1Sfqzyw9WOZ6qXcaIay7ZMDV9pXVyuoLTZB',
            mode: 'testnet',
        );

        $repository = $this->createMock(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);
        $bybitClient = $this->createMock(BybitClientInterface::class);

        $repository
            ->expects($this->once())
            ->method('revokeByUserId')
            ->with($userId)
        ;

        $bybitClient
            ->expects($this->once())
            ->method('get')
            ->with(
                '/v5/user/query-api',
                $this->isInstanceOf(BybitCredentialsDto::class),
                BybitEnvironmentEnum::Testnet,
            )
            ->willReturn(new BybitResponseDto(0, 'OK', [], [], 0))
        ;

        $encryptor->method('encrypt')->willReturn('encrypted-value');

        $connectionStub = $this->createStub(ApiConnection::class);
        $connectionStub->method('getId')->willReturn(42);
        $connectionStub->method('getUfCreatedAt')->willReturn(new DateTime());
        $connectionStub->method('getUfLastVerifiedAt')->willReturn(new DateTime());

        $repository
            ->expects($this->once())
            ->method('create')
            ->willReturn($connectionStub)
        ;

        $result = $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute($dto, $userId)
        ;

        self::assertInstanceOf(ApiConnectionResultDto::class, $result);
        self::assertTrue($result->connected);
        self::assertSame(42, $result->id);
        self::assertSame($userId, $result->userId);
        self::assertSame(ConnectionStatusEnum::Active, $result->status);
        self::assertSame(ConnectionModeEnum::Testnet, $result->mode);
        self::assertSame('EO0g**********UqxT', $result->maskedApiKey);
        self::assertNotNull($result->verifiedAt);
    }

    public function testFailedVerificationReturnsInvalidStatus(): void
    {
        $dto = new ConnectApiRequestDto(
            apiKey: 'bad-key-12345678',
            secretKey: 'bad-secret-12345678',
            mode: 'mainnet',
        );

        $repository = $this->createStub(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);
        $bybitClient = $this->createMock(BybitClientInterface::class);

        $bybitClient
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new BybitApiException('Invalid API key'))
        ;

        $encryptor->method('encrypt')->willReturn('enc');

        $connectionStub = $this->createStub(ApiConnection::class);
        $connectionStub->method('getId')->willReturn(10);
        $connectionStub->method('getUfCreatedAt')->willReturn(new DateTime());
        $connectionStub->method('getUfLastVerifiedAt')->willReturn(null);

        $repository->method('create')->willReturn($connectionStub);

        $result = $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute($dto, 2)
        ;

        self::assertSame(ConnectionStatusEnum::Invalid, $result->status);
        self::assertSame(ConnectionModeEnum::Mainnet, $result->mode);
        self::assertNull($result->verifiedAt);
    }

    public function testRevokesExistingConnectionBeforeCreatingNew(): void
    {
        $userId = 5;
        $dto = new ConnectApiRequestDto(
            apiKey: 'EO0gCaxwD79OuvUqxT',
            secretKey: 'x1Sfqzyw9WOZ6qXcaIay7ZMDV9pXVyuoLTZB',
            mode: 'testnet',
        );

        $callOrder = [];

        $repository = $this->createMock(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);
        $bybitClient = $this->createStub(BybitClientInterface::class);

        $repository
            ->expects($this->once())
            ->method('revokeByUserId')
            ->with($userId)
            ->willReturnCallback(function() use (&$callOrder): void {
                $callOrder[] = 'revoke';
            })
        ;

        $connectionStub = $this->createStub(ApiConnection::class);
        $connectionStub->method('getId')->willReturn(1);
        $connectionStub->method('getUfCreatedAt')->willReturn(new DateTime());
        $connectionStub->method('getUfLastVerifiedAt')->willReturn(new DateTime());

        $repository
            ->expects($this->once())
            ->method('create')
            ->willReturnCallback(function() use (&$callOrder, $connectionStub): ApiConnection {
                $callOrder[] = 'create';

                return $connectionStub;
            })
        ;

        $bybitClient->method('get')->willReturn(
            new BybitResponseDto(0, 'OK', [], [], 0),
        );
        $encryptor->method('encrypt')->willReturn('enc');

        $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute($dto, $userId)
        ;

        self::assertSame(['revoke', 'create'], $callOrder);
    }

    public function testMainnetModeMapsToMainnetEnvironment(): void
    {
        $dto = new ConnectApiRequestDto(
            apiKey: 'EO0gCaxwD79OuvUqxT',
            secretKey: 'secret',
            mode: 'mainnet',
        );

        $repository = $this->createStub(ApiConnectionRepository::class);
        $encryptor = $this->createStub(ApiKeyEncryptor::class);
        $bybitClient = $this->createMock(BybitClientInterface::class);

        $bybitClient
            ->expects($this->once())
            ->method('get')
            ->with(
                '/v5/user/query-api',
                $this->anything(),
                BybitEnvironmentEnum::Mainnet,
            )
            ->willReturn(new BybitResponseDto(0, 'OK', [], [], 0))
        ;

        $encryptor->method('encrypt')->willReturn('enc');

        $connectionStub = $this->createStub(ApiConnection::class);
        $connectionStub->method('getId')->willReturn(1);
        $connectionStub->method('getUfCreatedAt')->willReturn(new DateTime());
        $connectionStub->method('getUfLastVerifiedAt')->willReturn(new DateTime());

        $repository->method('create')->willReturn($connectionStub);

        $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute($dto, 1)
        ;
    }

    public function testEncryptsApiKeyAndSecret(): void
    {
        $dto = new ConnectApiRequestDto(
            apiKey: 'my-api-key-12345',
            secretKey: 'my-api-secret-12',
            mode: 'testnet',
        );

        $encryptedValues = [];

        $repository = $this->createMock(ApiConnectionRepository::class);
        $encryptor = $this->createMock(ApiKeyEncryptor::class);
        $bybitClient = $this->createStub(BybitClientInterface::class);

        $encryptor
            ->expects($this->exactly(2))
            ->method('encrypt')
            ->willReturnCallback(function(string $value) use (&$encryptedValues): string {
                $encryptedValues[] = $value;

                return 'encrypted_' . $value;
            })
        ;

        $connectionStub = $this->createStub(ApiConnection::class);
        $connectionStub->method('getId')->willReturn(1);
        $connectionStub->method('getUfCreatedAt')->willReturn(new DateTime());
        $connectionStub->method('getUfLastVerifiedAt')->willReturn(new DateTime());

        $repository
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->anything(),
                'encrypted_my-api-key-12345',
                'encrypted_my-api-secret-12',
                $this->anything(),
                $this->anything(),
                $this->anything(),
            )
            ->willReturn($connectionStub)
        ;

        $bybitClient->method('get')->willReturn(
            new BybitResponseDto(0, 'OK', [], [], 0),
        );

        $this->createUseCase($repository, $encryptor, $bybitClient)
            ->execute($dto, 1)
        ;

        self::assertSame(['my-api-key-12345', 'my-api-secret-12'], $encryptedValues);
    }
}
