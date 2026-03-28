<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Message\TradeStatusChangedMessage;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;
use Rebit\Share\Application\Contract\Notification\Enum\NotificationTypeEnum;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Balance\Message\SyncBalanceMessage;
use Rebit\Exchange\Domain\Trade\Entity\Trade;

/**
 * Handler очереди tradeEvent: обработка смены статуса сделки.
 */
final readonly class TradeStatusChangedMessageHandler
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private ChatScriptExecutionRepository $chatScriptExecutionRepository,
        private MessagePublisherInterface $balanceSyncPublisher,
        private NotificationPublisherInterface $notificationPublisher,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function __invoke(TradeStatusChangedMessage $message): void
    {
        $trade = $this->tradeRepository->findById($message->tradeId);
        if (null === $trade) {
            $this->logger->warning('TradeStatusChangedMessage пропущено: сделка не найдена', [
                'tradeId' => $message->tradeId,
                'oldStatus' => $message->oldStatus,
                'newStatus' => $message->newStatus,
            ]);

            return;
        }

        $newStatus = TradeStatusEnum::tryFrom($message->newStatus);
        $localUserId = $this->resolveLocalUserId($trade);

        if (null !== $newStatus && false === $newStatus->isChatActive()) {
            $this->chatScriptExecutionRepository->cancelByTradeId($trade->getId());
        }

        if (TradeStatusEnum::Completed === $newStatus && 0 < $localUserId) {
            $this->publishBalanceSync($localUserId, $trade->getId(), 'localUser');
        }

        if (0 < $localUserId) {
            $this->publishStatusChangedNotification($trade, $localUserId, $message->oldStatus, $message->newStatus);
        } else {
            $this->logger->warning('Пропущено уведомление о смене статуса: локальный пользователь сделки не определён', [
                'tradeId' => $trade->getId(),
                'side' => $trade->getUfSide(),
                'oldStatus' => $message->oldStatus,
                'newStatus' => $message->newStatus,
            ]);
        }

        $this->logger->info('TradeStatusChangedMessage получено', [
            'tradeId' => $message->tradeId,
            'oldStatus' => $message->oldStatus,
            'newStatus' => $message->newStatus,
        ]);
    }

    private function publishBalanceSync(int $userId, int $tradeId, string $role): void
    {
        if (0 >= $userId) {
            return;
        }

        try {
            $this->balanceSyncPublisher->dispatch(
                new SyncBalanceMessage(
                    userId: $userId,
                ),
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Не удалось поставить синхронизацию баланса по событию сделки', [
                'tradeId' => $tradeId,
                'userId' => $userId,
                'role' => $role,
                'error' => $exception->getMessage(),
                'exceptionClass' => $exception::class,
            ]);

            return;
        }

        $this->logger->info('Поставлена синхронизация баланса по событию сделки', [
            'tradeId' => $tradeId,
            'userId' => $userId,
            'role' => $role,
        ]);
    }

    private function publishStatusChangedNotification(
        Trade $trade,
        int $localUserId,
        string $oldStatus,
        string $newStatus,
    ): void {
        try {
            $this->notificationPublisher->publish(
                new SendNotificationDto(
                    type: NotificationTypeEnum::TRADE_STATUS_CHANGED->value,
                    userId: $localUserId,
                    payload: [
                        'tradeId' => (string)$trade->getId(),
                        'oldStatus' => $oldStatus,
                        'newStatus' => $newStatus,
                        'counterpartyName' => $trade->getUfCounterpartyName(),
                    ],
                ),
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Не удалось отправить уведомление о смене статуса сделки', [
                'tradeId' => $trade->getId(),
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
                'error' => $exception->getMessage(),
                'exceptionClass' => $exception::class,
            ]);
        }
    }

    private function resolveLocalUserId(Trade $trade): int
    {
        return match ($trade->getUfSide()) {
            'buy' => $trade->getUfBuyerUserId(),
            'sell' => $trade->getUfSellerUserId(),
            default => 0,
        };
    }
}
