<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Message\TradeDiscoveredMessage;
use Rebit\Exchange\Application\Trade\UseCase\EnrichTradeFromBybitUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\StartTradeChatScriptUseCase;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;
use Rebit\Share\Application\Contract\Notification\Enum\NotificationTypeEnum;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Handler очереди tradeEvent: обработка обнаружения сделки.
 */
final readonly class TradeDiscoveredMessageHandler
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private EnrichTradeFromBybitUseCase $enrichTradeFromBybitUseCase,
        private NotificationPublisherInterface $notificationPublisher,
        private StartTradeChatScriptUseCase $startTradeChatScriptUseCase,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function __invoke(TradeDiscoveredMessage $message): void
    {
        $trade = $this->tradeRepository->findById($message->tradeId);
        if (null === $trade) {
            $this->logger->warning('TradeDiscoveredMessage пропущено: сделка не найдена', [
                'tradeId' => $message->tradeId,
                'bybitOrderId' => $message->bybitOrderId,
            ]);

            return;
        }

        try {
            $this->enrichTradeFromBybitUseCase->execute($trade);
        } catch (\Throwable $exception) {
            $this->logger->error('Не удалось обогатить сделку деталями Bybit', [
                'tradeId' => $trade->getId(),
                'bybitOrderId' => $trade->getUfBybitOrderId(),
                'error' => $exception->getMessage(),
                'exceptionClass' => $exception::class,
            ]);
        }

        try {
            $this->notificationPublisher->publish(
                new SendNotificationDto(
                    type: NotificationTypeEnum::TRADE_DISCOVERED->value,
                    userId: $trade->getUfBuyerUserId(),
                    payload: [
                        'tradeId' => (string)$trade->getId(),
                        'side' => $trade->getUfSide(),
                        'fiatAmount' => (string)$trade->getUfFiatAmount(),
                        'counterpartyName' => $trade->getUfCounterpartyName(),
                    ],
                ),
            );
        } catch (\Throwable $exception) {
            $this->logger->error('Не удалось отправить уведомление по новой сделке из tradeEvent handler', [
                'tradeId' => $trade->getId(),
                'error' => $exception->getMessage(),
                'exceptionClass' => $exception::class,
            ]);
        }

        try {
            $this->startTradeChatScriptUseCase->execute($trade);
        } catch (\Throwable $exception) {
            $this->logger->error('Не удалось запустить чат-скрипт по новой сделке', [
                'tradeId' => $trade->getId(),
                'error' => $exception->getMessage(),
                'exceptionClass' => $exception::class,
            ]);
        }

        $this->logger->info('TradeDiscoveredMessage получено', [
            'tradeId' => $message->tradeId,
            'bybitOrderId' => $message->bybitOrderId,
        ]);
    }
}
