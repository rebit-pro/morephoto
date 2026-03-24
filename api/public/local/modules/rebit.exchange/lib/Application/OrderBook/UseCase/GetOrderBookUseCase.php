<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\UseCase;

use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookBothSidesResultDto;
use Rebit\Exchange\Application\OrderBook\Dto\Result\OrderBookEntryResultDto;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение стакана ордеров (buy + sell) из локальной БД по символам токена и фиата.
 *
 * Bybit хранит способы оплаты как числовые ID (например: ["14","18","40"]).
 * UseCase резолвит их в UF_CODE локального справочника (SBP, TINKOFF, SBERBANK).
 * Если Bybit ID не найден в справочнике — передаётся как есть (строка).
 */
final readonly class GetOrderBookUseCase
{
    public function __construct(
        private OrderBookRepository $orderBookRepository,
        private CurrencyPairRepository $currencyPairRepository,
        private PaymentMethodRepository $paymentMethodRepository,
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

        // Собираем все уникальные Bybit payment IDs из всех записей стакана
        $allBybitIds = [];
        $rawPayments = [];
        foreach ($entries as $entry) {
            $decoded = json_decode($entry->getUfPaymentMethodIds() ?: '[]', true, 512, JSON_THROW_ON_ERROR);
            $raw = is_array($decoded) ? $decoded : [];
            $rawPayments[$entry->getId()] = $raw;
            foreach ($raw as $bybitId) {
                $allBybitIds[(string)$bybitId] = true;
            }
        }

        // Один запрос к БД для резолвинга всех Bybit ID → UF_CODE
        $bybitToCode = [] !== $allBybitIds
            ? $this->paymentMethodRepository->mapBybitIdsToCode(array_keys($allBybitIds))
            : [];

        $items = [];
        foreach ($entries as $entry) {
            $raw = $rawPayments[$entry->getId()] ?? [];

            // Заменяем Bybit ID на UF_CODE; если ID неизвестен — оставляем как есть
            $paymentCodes = array_map(
                static fn(string $bybitId): string => $bybitToCode[$bybitId] ?? $bybitId,
                array_map('strval', $raw),
            );

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
                paymentMethods: $paymentCodes,
                paymentTimeLimit: $entry->getUfPaymentTimeLimit(),
            );
        }

        return $items;
    }
}
