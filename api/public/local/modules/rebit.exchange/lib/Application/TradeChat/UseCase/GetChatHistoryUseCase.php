<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Rebit\Exchange\Application\TradeChat\Dto\Result\TradeMessageListResultDto;
use Rebit\Exchange\Application\TradeChat\Dto\Result\TradeMessageResultDto;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Facade\Cache;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение истории чата из локальной БД с предварительной синхронизацией из Bybit.
 */
final readonly class GetChatHistoryUseCase
{
    private const int SYNC_TTL_SECONDS = 10;

    public function __construct(
        private TradeMessageRepository $messageRepository,
        private TradeRepository $tradeRepository,
        private SyncChatMessagesUseCase $syncChatMessages,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $tradeId, int $userId): TradeMessageListResultDto
    {
        $trade = $this->tradeRepository->findById($tradeId);

        if (null === $trade) {
            throw new EntityNotFoundException('Сделка не найдена');
        }

        if ($trade->getUfBuyerUserId() !== $userId && $trade->getUfSellerUserId() !== $userId) {
            throw new HttpException('Нет доступа к чату этой сделки', 403);
        }

        $this->syncMessagesThrottled($tradeId, $trade, $userId);

        $messages = $this->messageRepository->findByTradeId($tradeId);

        $items = [];
        foreach ($messages as $msg) {
            $items[] = new TradeMessageResultDto(
                id: $msg->getId(),
                tradeId: $msg->getUfTradeId(),
                userId: $msg->getUfUserId(),
                message: $msg->getUfMessage(),
                messageType: $msg->getUfMessageType(),
                contentType: $msg->getUfContentType(),
                fileName: $msg->getUfFileName() ?: null,
                createdAt: $msg->getUfCreatedAt()?->format('c'),
            );
        }

        return new TradeMessageListResultDto($items);
    }

    /**
     * Сдерживаем polling фронтенда и не дёргаем Bybit чаще одного раза в несколько секунд на сделку/пользователя.
     * При проблемах с кешем деградируем в обычный sync, чтобы не ломать чат.
     *
     * @throws HttpException
     * @throws RepositoryException
     */
    private function syncMessagesThrottled(int $tradeId, Trade $trade, int $userId): void
    {
        try {
            Cache::remember(
                function() use ($trade, $userId): bool {
                    $this->syncChatMessages->execute($trade, $userId);

                    return true;
                },
                sprintf('rebit_exchange_trade_chat_sync_%d_%d', $tradeId, $userId),
                self::SYNC_TTL_SECONDS,
            );

            return;
        } catch (HttpException|RepositoryException $e) {
            throw $e;
        } catch (\Throwable) {
            $this->syncChatMessages->execute($trade, $userId);
        }
    }
}
