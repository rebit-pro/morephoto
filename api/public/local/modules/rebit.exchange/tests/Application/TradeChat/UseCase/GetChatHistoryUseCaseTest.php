<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\TradeChat\UseCase;

use Bitrix\Main\Type\DateTime;
use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\TradeChat\Dto\Result\TradeMessageListResultDto;
use Rebit\Exchange\Application\TradeChat\UseCase\GetChatHistoryUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\SyncChatMessagesUseCase;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Entity\TradeMessage;
use Rebit\Exchange\Domain\TradeChat\Entity\TradeMessageCollection;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * @internal
 */
final class GetChatHistoryUseCaseTest extends TestCase
{
    private const int BUYER_ID = 10;
    private const int SELLER_ID = 20;

    public function testSuccessfulChatHistoryRetrieval(): void
    {
        $trade = $this->createTradeStub();

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $msg = $this->createMessageStub(1, 'Привет!');

        $collection = $this->createStub(TradeMessageCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$msg]));

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $msgRepo = $this->createMock($messageRepositoryClass);
        $msgRepo
            ->expects($this->once())
            ->method('findByTradeId')
            ->with(1)
            ->willReturn($collection)
        ;

        /** @var class-string<SyncChatMessagesUseCase> $syncChatMessagesClass */
        $syncChatMessagesClass = SyncChatMessagesUseCase::class;
        $syncChatMessages = $this->createMock($syncChatMessagesClass);
        $syncChatMessages
            ->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($trade), self::BUYER_ID)
            ->willReturn(1)
        ;

        $result = (new GetChatHistoryUseCase($msgRepo, $tradeRepo, $syncChatMessages))->execute(1, self::BUYER_ID);

        self::assertInstanceOf(TradeMessageListResultDto::class, $result);
        self::assertCount(1, $result->items);
        self::assertSame('Привет!', $result->items[0]->message);
    }

    public function testEmptyChatHistory(): void
    {
        $trade = $this->createTradeStub();

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        $collection = $this->createStub(TradeMessageCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $msgRepo = $this->createStub($messageRepositoryClass);
        $msgRepo->method('findByTradeId')->willReturn($collection);

        /** @var class-string<SyncChatMessagesUseCase> $syncChatMessagesClass */
        $syncChatMessagesClass = SyncChatMessagesUseCase::class;
        $syncChatMessages = $this->createMock($syncChatMessagesClass);
        $syncChatMessages
            ->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($trade), self::SELLER_ID)
            ->willReturn(0)
        ;

        $result = (new GetChatHistoryUseCase($msgRepo, $tradeRepo, $syncChatMessages))->execute(1, self::SELLER_ID);

        self::assertSame([], $result->items);
    }

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function testTradeNotFoundThrows404(): void
    {
        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn(null);

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $msgRepo = $this->createStub($messageRepositoryClass);

        /** @var class-string<SyncChatMessagesUseCase> $syncChatMessagesClass */
        $syncChatMessagesClass = SyncChatMessagesUseCase::class;
        $syncChatMessages = $this->createStub($syncChatMessagesClass);

        $this->expectException(EntityNotFoundException::class);

        new GetChatHistoryUseCase($msgRepo, $tradeRepo, $syncChatMessages)->execute(1, self::BUYER_ID);
    }

    public function testAccessDeniedForNonParticipantThrows403(): void
    {
        $trade = $this->createTradeStub();

        $tradeRepo = $this->createStub(TradeRepository::class);
        $tradeRepo->method('findById')->willReturn($trade);

        /** @var class-string<TradeMessageRepository> $messageRepositoryClass */
        $messageRepositoryClass = TradeMessageRepository::class;
        $msgRepo = $this->createStub($messageRepositoryClass);

        /** @var class-string<SyncChatMessagesUseCase> $syncChatMessagesClass */
        $syncChatMessagesClass = SyncChatMessagesUseCase::class;
        $syncChatMessages = $this->createStub($syncChatMessagesClass);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new GetChatHistoryUseCase($msgRepo, $tradeRepo, $syncChatMessages))->execute(1, 999);
    }

    private function createTradeStub(): Trade
    {
        $trade = $this->createStub(Trade::class);
        $trade->method('getUfBuyerUserId')->willReturn(self::BUYER_ID);
        $trade->method('getUfSellerUserId')->willReturn(self::SELLER_ID);

        return $trade;
    }

    private function createMessageStub(int $id, string $message): TradeMessage
    {
        $now = new DateTime();
        $msg = $this->createStub(TradeMessage::class);
        $msg->method('getId')->willReturn($id);
        $msg->method('getUfTradeId')->willReturn(1);
        $msg->method('getUfUserId')->willReturn(self::BUYER_ID);
        $msg->method('getUfMessage')->willReturn($message);
        $msg->method('getUfMessageType')->willReturn('user');
        $msg->method('getUfContentType')->willReturn('str');
        $msg->method('getUfFileName')->willReturn('');
        $msg->method('getUfCreatedAt')->willReturn($now);

        return $msg;
    }
}
