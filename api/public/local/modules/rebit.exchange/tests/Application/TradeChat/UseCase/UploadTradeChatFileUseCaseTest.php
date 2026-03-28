<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\TradeChat\Dto\Request\UploadTradeChatFileRequestDto;
use Rebit\Exchange\Application\TradeChat\Dto\Result\UploadTradeChatFileResultDto;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\UploadTradeChatFileUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Infrastructure\Bitrix\TradeChatUploadFileLocator;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class UploadTradeChatFileUseCaseTest extends TestCase
{
    private const int BUYER_ID = 10;
    private const int SELLER_ID = 20;

    public function testSuccessfulUpload(): void
    {
        $dto = new UploadTradeChatFileRequestDto(
            tradeId: 1,
            fileId: 15,
        );

        $tradeRepository = $this->createStub(TradeRepository::class);
        $tradeRepository->method('findById')->willReturn($this->createTradeStub());

        $fileLocator = $this->createMock(TradeChatUploadFileLocator::class);
        $fileLocator
            ->expects($this->once())
            ->method('getById')
            ->with(15)
            ->willReturn([
                'path' => '/tmp/test.png',
                'name' => 'test.png',
                'mimeType' => 'image/png',
            ])
        ;

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway
            ->expects($this->once())
            ->method('uploadFile')
            ->with(self::BUYER_ID, '/tmp/test.png', 'test.png', 'image/png')
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
        $fileLocator = $this->createStub(TradeChatUploadFileLocator::class);

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
        $fileLocator = $this->createStub(TradeChatUploadFileLocator::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new UploadTradeChatFileUseCase($tradeRepository, $chatGateway, $fileLocator))
            ->execute($dto, 999)
        ;
    }

    private function createTradeStub(): Trade
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);

        return $trade;
    }
}
