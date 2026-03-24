<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\UseCase;

use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookEntryResultDto;
use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookListResultDto;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение стакана ордеров из локальной БД.
 */
final readonly class GetOrderBookUseCase
{
    public function __construct(
        private OrderBookRepository $orderBookRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $currencyPairId, string $side): OrderBookListResultDto
    {
        $entries = $this->orderBookRepository->findByCurrencyPairAndSide($currencyPairId, $side);

        $items = [];
        foreach ($entries as $entry) {
            $paymentIds = json_decode($entry->getUfPaymentMethodIds() ?: '[]', true);

            $items[] = new OrderBookEntryResultDto(
                id: $entry->getId(),
                bybitOrderId: $entry->getUfBybitOrderId(),
                side: $entry->getUfSide(),
                price: $entry->getUfPrice(),
                quantity: $entry->getUfQuantity(),
                minAmount: $entry->getUfMinAmount(),
                maxAmount: $entry->getUfMaxAmount(),
                counterpartyName: $entry->getUfCounterpartyName(),
                counterpartyRating: $entry->getUfCounterpartyRating(),
                counterpartyTrades: $entry->getUfCounterpartyTrades(),
                counterpartyCompletionRate: $entry->getUfCounterpartyCompletionRate(),
                paymentMethodIds: is_array($paymentIds) ? $paymentIds : [],
                paymentTimeLimit: $entry->getUfPaymentTimeLimit(),
            );
        }

        return new OrderBookListResultDto($items);
    }
}
