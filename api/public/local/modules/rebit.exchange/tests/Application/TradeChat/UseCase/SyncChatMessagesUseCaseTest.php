<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\SyncChatMessagesUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;

/**
 * @internal
 */
final class SyncChatMessagesUseCaseTest extends TestCase
{
    private const int USER_ID = 10;
    private const int TRADE_ID = 123;

    public function testReturnsZeroWhenTradeHasNoBybitOrderId(): void
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBybitOrderId')->willReturn('');

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $messageRepository = $this->createMock($messageRepositoryClass);
        $messageRepository->expects($this->never())->method('existsByBybitMsgUuid');
        $messageRepository->expects($this->never())->method('create');

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway->expects($this->never())->method('fetchMessages');

        $result = (new SyncChatMessagesUseCase($messageRepository, $chatGateway))
            ->execute($trade, self::USER_ID);

        self::assertSame(0, $result);
    }

    public function testReturnsZeroWhenBybitHasNoMessages(): void
    {
        $trade = $this->createTradeStub();

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $messageRepository = $this->createMock($messageRepositoryClass);
        $messageRepository->expects($this->never())->method('existsByBybitMsgUuid');
        $messageRepository->expects($this->never())->method('create');

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway
            ->expects($this->once())
            ->method('fetchMessages')
            ->with(self::USER_ID, 'bybit-order-123', 1, 50)
            ->willReturn([])
        ;

        $result = (new SyncChatMessagesUseCase($messageRepository, $chatGateway))
            ->execute($trade, self::USER_ID);

        self::assertSame(0, $result);
    }

    public function testSkipsDuplicateMessagesByBybitUuid(): void
    {
        $trade = $this->createTradeStub();

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $messageRepository = $this->createMock($messageRepositoryClass);
        $messageRepository
            ->expects($this->once())
            ->method('existsByBybitMsgUuid')
            ->with(self::TRADE_ID, 'msg-1')
            ->willReturn(true)
        ;
        $messageRepository->expects($this->never())->method('create');

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway
            ->expects($this->once())
            ->method('fetchMessages')
            ->willReturn([
                [
                    'id' => 'msg-1',
                    'message' => 'duplicate',
                    'contentType' => 'str',
                    'fileName' => '',
                    'userId' => 'bybit-user-1',
                    'nickName' => 'Trader',
                    'createDate' => '2026-03-28T09:00:00+00:00',
                ],
            ])
        ;

        $result = (new SyncChatMessagesUseCase($messageRepository, $chatGateway))
            ->execute($trade, self::USER_ID);

        self::assertSame(0, $result);
    }

    public function testImportsNewMessageAndMapsAttachmentFields(): void
    {
        $trade = $this->createTradeStub();

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $messageRepository = $this->createMock($messageRepositoryClass);
        $messageRepository
            ->expects($this->once())
            ->method('existsByBybitMsgUuid')
            ->with(self::TRADE_ID, 'msg-2')
            ->willReturn(false)
        ;
        $messageRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                tradeId: self::TRADE_ID,
                userId: 0,
                message: 'https://cdn.bybit.test/file.png',
                messageType: MessageTypeEnum::User,
                contentType: ContentTypeEnum::Pic,
                bybitMsgUuid: 'msg-2',
                fileName: 'receipt.png',
            )
        ;

        $chatGateway = $this->createMock(BybitChatGatewayInterface::class);
        $chatGateway
            ->expects($this->once())
            ->method('fetchMessages')
            ->willReturn([
                [
                    'id' => 'msg-2',
                    'message' => 'https://cdn.bybit.test/file.png',
                    'contentType' => 'pic',
                    'fileName' => 'receipt.png',
                    'userId' => 'bybit-user-2',
                    'nickName' => 'Trader',
                    'createDate' => '2026-03-28T09:01:00+00:00',
                ],
            ])
        ;

        $result = (new SyncChatMessagesUseCase($messageRepository, $chatGateway))
            ->execute($trade, self::USER_ID);

        self::assertSame(1, $result);
    }

    private function createTradeStub(): Trade
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getId')->willReturn(self::TRADE_ID);
        $trade->method('getUfBybitOrderId')->willReturn('bybit-order-123');

        return $trade;
    }
}
