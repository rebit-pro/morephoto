<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\UseCase;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\OrderBook\Port\BybitOrderBookGatewayInterface;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Domain\Shared\Enum\SideEnum;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Синхронизация стакана ордеров с Bybit API.
 * Перезаписывает локальные данные для каждой пары + направления.
 */
final readonly class SyncOrderBookUseCase
{
    public function __construct(
        private OrderBookRepository $orderBookRepository,
        private CurrencyPairRepository $currencyPairRepository,
        private BybitOrderBookGatewayInterface $orderBookGateway,
        private LoggerInterface $logger,
    ) {}

    /**
     * Синхронизирует стакан для всех активных валютных пар.
     *
     * @throws RepositoryException
     */
    public function execute(int $userId): void
    {
        $pairs = $this->currencyPairRepository->findActive();

        foreach ($pairs as $pair) {
            foreach (SideEnum::cases() as $side) {
                $this->syncPairSide(
                    $userId,
                    $pair->getId(),
                    $pair->getUfCode(),
                    $side,
                );
            }
        }
    }

    /**
     * @throws RepositoryException
     */
    private function syncPairSide(
        int $userId,
        int $currencyPairId,
        string $pairCode,
        SideEnum $side,
    ): void {
        // Пара формата "USDT_RUB" → tokenId = "USDT", currencyId = "RUB"
        $parts = explode('_', $pairCode);
        if (2 !== count($parts)) {
            $this->logger->warning('Invalid pair code format', ['pairCode' => $pairCode]);

            return;
        }

        [$tokenId, $currencyId] = $parts;

        try {
            $orderBook = $this->orderBookGateway->fetchOrderBook(
                $userId,
                $tokenId,
                $currencyId,
                $side->toBybit(),
            );
        } catch (HttpException $e) {
            $this->logger->warning('OrderBook sync failed', [
                'pairCode' => $pairCode,
                'side' => $side->value,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $entries = [];
        foreach ($orderBook->items as $item) {
            $entries[] = [
                'bybitOrderId' => $item->id,
                'currencyPairId' => $currencyPairId,
                'side' => $side->value,
                'price' => (float)$item->price,
                'quantity' => (float)$item->lastQuantity,
                'minAmount' => (float)$item->minAmount,
                'maxAmount' => (float)$item->maxAmount,
                'counterpartyName' => $item->nickName,
                'counterpartyRating' => 0.0,
                'counterpartyTrades' => $item->recentOrderNum,
                'counterpartyCompletionRate' => $item->recentExecuteRate,
                'paymentMethodIds' => json_encode($item->payments, JSON_THROW_ON_ERROR),
                'paymentTimeLimit' => $item->paymentPeriod,
            ];
        }

        $this->orderBookRepository->replaceByCurrencyPairAndSide(
            $currencyPairId,
            $side->value,
            $entries,
        );
    }
}
