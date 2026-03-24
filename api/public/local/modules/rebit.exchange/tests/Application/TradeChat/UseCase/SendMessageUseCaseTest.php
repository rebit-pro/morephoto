<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\TradeChat\Dto\Request\SendMessageRequestDto;
use Rebit\Exchange\Application\TradeChat\Dto\Result\TradeMessageResultDto;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\SendMessageUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class SendMessageUseCaseTest extends TestCase
{
    private const int BUYER_ID = 10;
    private const int SELLER_ID = 20;

    public function testSuccessfulMessageSend(): void
    {
        $dto = new SendMessageRequestDto(
            tradeId: 1,
            message: 'Привет!',
            contentType: 'str',
        );

        $trade = $this->createTradeStub();

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway
            ->expects($this->once())
            ->method('sendMessage')
            ->with(
                self::BUYER_ID,
                'bybit-order-1',
                'Привет!',
                'str',
                $this->isType('string'),
                null,
            )
        ;

        $msg = $this->createMessageStub();
        $msgRepo = $this->createMock(TradeMessageRepository::class);
        $msgRepo
            ->expects($this->once())
            ->method('create')
            ->willReturn($msg)
        ;

        $result = (new SendMessageUseCase($msgRepo, $tradeRepo, $chatGateway))
            ->execute($dto, self::BUYER_ID)
        ;

        self::assertInstanceOf(TradeMessageResultDto::class, $result);
        self::assertSame(1, $result->id);
        self::assertSame('Привет!', $result->message);
    }

    public function testTradeNotFoundThrows404(): void
    {
        $dto = new SendMessageRequestDto(tradeId: 1, message: 'test');

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn(null);

        $msgRepo = $this->createStub(TradeMessageRepository::class);
        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);

        $this->expectException(EntityNotFoundException::class);

        (new SendMessageUseCase($msgRepo, $tradeRepo, $chatGateway))
            ->execute($dto, self::BUYER_ID)
        ;
    }

    public function testAccessDeniedForNonParticipantThrows403(): void
    {
        $dto = new SendMessageRequestDto(tradeId: 1, message: 'test');

        $trade = $this->createTradeStub();
        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $msgRepo = $this->createStub(TradeMessageRepository::class);
        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new SendMessageUseCase($msgRepo, $tradeRepo, $chatGateway))
            ->execute($dto, 999)
        ;
    }

    public function testEmptyMessageThrowsValidation(): void
    {
        $dto = new SendMessageRequestDto(tradeId: 1, message: '   ');

        $trade = $this->createTradeStub();
        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $msgRepo = $this->createStub(TradeMessageRepository::class);
        $chatGateway = $this->createStub(BybitChatGatewayInterface::class);

        $this->expectException(ValidationHttpException::class);
        $this->expectExceptionMessage('Сообщение не может быть пустым');

        (new SendMessageUseCase($msgRepo, $tradeRepo, $chatGateway))
            ->execute($dto, self::BUYER_ID)
        ;
    }

    private function createTradeStub(): Trade
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-1');

        return $trade;
    }

    private function createMessageStub(): TradeMessage
    {
        $now = new DateTime();
        $msg = $this->createStub(TradeMessage::class);
        $msg->method('getId')->willReturn(1);
        $msg->method('getUfTradeId')->willReturn(1);
        $msg->method('getUfUserId')->willReturn(self::BUYER_ID);
        $msg->method('getUfMessage')->willReturn('Привет!');
        $msg->method('getUfMessageType')->willReturn('user');
        $msg->method('getUfContentType')->willReturn('str');
        $msg->method('getUfFileName')->willReturn('');
        $msg->method('getUfCreatedAt')->willReturn($now);

        return $msg;
    }
}
