<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use Bitrix\Main\Data\ManagedCache;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\TradeChat\Dto\Request\UploadTradeChatFileRequestDto;
use Rebit\Exchange\Application\TradeChat\Dto\Result\UploadTradeChatFileResultDto;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\UploadTradeChatFileUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Infrastructure\Bitrix\TradeChatUploadFileLocator;
use Rebit\Share\Domain\File\Service\UploadedFileOwnershipService;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class UploadTradeChatFileUseCaseTest extends TestCase
{
    private const int BUYER_ID = 10;
    private const int SELLER_ID = 20;

    /** @var list<string> */
    private array $createdFiles = [];

    private ?string $previousDocumentRoot = null;

    protected function tearDown(): void
    {
        \CFile::resetMockFiles();

        foreach ($this->createdFiles as $filePath) {
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        if (null === $this->previousDocumentRoot) {
            unset($_SERVER['DOCUMENT_ROOT']);
        } else {
            $_SERVER['DOCUMENT_ROOT'] = $this->previousDocumentRoot;
        }

        $this->createdFiles = [];
        $this->previousDocumentRoot = null;

        parent::tearDown();
    }

    public function testSuccessfulUpload(): void
    {
        $dto = new UploadTradeChatFileRequestDto(
            tradeId: 1,
            fileId: 15,
        );

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($this->createTradeStub());

        $fileLocator = $this->createFileLocator(fileId: 15, userId: self::BUYER_ID, moduleId: 'rebit.exchange');

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway
            ->expects($this->once())
            ->method('uploadFile')
            ->with(self::BUYER_ID, $this->createdFiles[0], 'test.png', 'image/png')
            ->willReturn([
                'url' => 'https://api-testnet.bybit.com/fiat/p2p/oss/showObj/test.png',
                'type' => 'IMAGE',
            ])
        ;

        $result = (new UploadTradeChatFileUseCase($tradeRepository, $chatGateway, $fileLocator))
            ->execute($dto, self::BUYER_ID)
        ;

        self::assertInstanceOf(UploadTradeChatFileResultDto::class, $result);
        self::assertSame('test.png', $result->fileName);
        self::assertSame('https://api-testnet.bybit.com/fiat/p2p/oss/showObj/test.png', $result->fileUrl);
        self::assertSame('pic', $result->contentType);
    }

    public function testTradeNotFoundThrows404(): void
    {
        $dto = new UploadTradeChatFileRequestDto(
            tradeId: 1,
            fileId: 15,
        );

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn(null);

        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);
        $fileLocator = $this->createEmptyFileLocator();

        $this->expectException(EntityNotFoundException::class);

        (new UploadTradeChatFileUseCase($tradeRepository, $chatGateway, $fileLocator))
            ->execute($dto, self::BUYER_ID)
        ;
    }

    public function testAccessDeniedForNonParticipantThrows403(): void
    {
        $dto = new UploadTradeChatFileRequestDto(
            tradeId: 1,
            fileId: 15,
        );

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($this->createTradeStub());

        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);
        $fileLocator = $this->createEmptyFileLocator();

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new UploadTradeChatFileUseCase($tradeRepository, $chatGateway, $fileLocator))
            ->execute($dto, 999)
        ;
    }

    public function testFileOwnershipViolationThrows403(): void
    {
        $dto = new UploadTradeChatFileRequestDto(
            tradeId: 1,
            fileId: 15,
        );

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($this->createTradeStub());

        $fileLocator = $this->createFileLocator(fileId: 15, userId: 999, moduleId: 'rebit.exchange');

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->never())->method('uploadFile');

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new UploadTradeChatFileUseCase($tradeRepository, $chatGateway, $fileLocator))
            ->execute($dto, self::BUYER_ID)
        ;
    }

    private function createTradeStub(): Trade
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);

        return $trade;
    }

    private function createEmptyFileLocator(): TradeChatUploadFileLocator
    {
        return new TradeChatUploadFileLocator(
            new UploadedFileOwnershipService(new InMemoryManagedCache()),
        );
    }

    private function createFileLocator(int $fileId, int $userId, string $moduleId): TradeChatUploadFileLocator
    {
        $ownershipService = new UploadedFileOwnershipService(new InMemoryManagedCache());
        $ownershipService->remember($fileId, $userId, $moduleId);

        $rootPath = sys_get_temp_dir() . '/rebit-trade-chat-tests';
        $uploadDir = $rootPath . '/upload/rebit.exchange';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . '/test.png';
        file_put_contents($filePath, 'test');
        $this->createdFiles[] = $filePath;

        $this->previousDocumentRoot = $_SERVER['DOCUMENT_ROOT'] ?? null;
        $_SERVER['DOCUMENT_ROOT'] = $rootPath;

        \CFile::setMockFileArray($fileId, [
            'ID' => $fileId,
            'MODULE_ID' => $moduleId,
            'SRC' => '/upload/rebit.exchange/test.png',
            'ORIGINAL_NAME' => 'test.png',
            'FILE_NAME' => 'test.png',
            'CONTENT_TYPE' => 'image/png',
            'FILE_SIZE' => 4,
        ]);

        return new TradeChatUploadFileLocator($ownershipService);
    }
}

final class InMemoryManagedCache extends ManagedCache
{
    /** @var array<string, mixed> */
    private array $storage = [];

    // noinspection PhpParameterNameChangedDuringInheritanceInspection
    public function read(int $ttl, string $uniqueId, string $tableId = ''): bool
    {
        return array_key_exists($uniqueId, $this->storage);
    }

    // noinspection PhpParameterNameChangedDuringInheritanceInspection
    public function get(string $uniqueId): mixed
    {
        return $this->storage[$uniqueId] ?? null;
    }

    // noinspection PhpParameterNameChangedDuringInheritanceInspection
    public function set(string $uniqueId, mixed $val): void
    {
        $this->storage[$uniqueId] = $val;
    }

    // noinspection PhpParameterNameChangedDuringInheritanceInspection
    public function clean(string $uniqueId, string $tableId = ''): void
    {
        unset($this->storage[$uniqueId]);
    }
}
