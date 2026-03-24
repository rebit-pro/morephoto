<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\UseCase;

use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookBothSidesResultDto;
use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookEntryResultDto;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение стакана ордеров (buy + sell) из локальной БД по символам токена и фиата.
 */
final readonly class GetOrderBookUseCase
{
    public function __construct(
        private OrderBookRepository $orderBookRepository,
        private CurrencyPairRepository $currencyPairRepository,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     * @throws \JsonException
     */
    public function execute(string $token, string $fiat): OrderBookBothSidesResultDto
    {
        $pair = $this->currencyPairRepository->findByTokenAndFiat($token, $fiat);
        if (null === $pair) {
            throw new HttpException(
                sprintf('Валютная пара %s/%s не найдена.', $token, $fiat),
                404,
            );
        }
        $pairId = $pair->getId();

        return new OrderBookBothSidesResultDto(
            buy: $this->buildEntries($pairId, 'buy'),
            sell: $this->buildEntries($pairId, 'sell'),
        );
    }

    /**
     * @return array<int, OrderBookEntryResultDto>
     *
     * @throws RepositoryException
     * @throws \JsonException
     */
    private function buildEntries(int $pairId, string $side): array
    {
        $entries = $this->orderBookRepository->findByCurrencyPairAndSide($pairId, $side);
        $items = [];
        foreach ($entries as $entry) {
            $paymentIds = json_decode($entry->getUfPaymentMethodIds() ?: '[]', true, 512, JSON_THROW_ON_ERROR);
            $items[] = new OrderBookEntryResultDto(
                id: $entry->getId(),
                bybitOrderId: $entry->getUfBybitOrderId(),
                side: $entry->getUfSide(),
                price: $entry->getUfPrice(),
                amount: $entry->getUfQuantity(),
                minLimit: $entry->getUfMinAmount(),
                maxLimit: $entry->getUfMaxAmount(),
                username: $entry->getUfCounterpartyName(),
                counterpartyRating: $entry->getUfCounterpartyRating(),
                completedTrades: $entry->getUfCounterpartyTrades(),
                completionRate: $entry->getUfCounterpartyCompletionRate(),
                paymentMethods: is_array($paymentIds) ? $paymentIds : [],
                paymentTimeLimit: $entry->getUfPaymentTimeLimit(),
            );
        }

        return $items;
    }
}
