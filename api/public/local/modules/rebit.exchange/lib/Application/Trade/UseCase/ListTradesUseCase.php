<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Rebit\Exchange\Application\Trade\Dto\Result\TradeListResultDto;
use Rebit\Exchange\Application\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение списка сделок пользователя.
 */
final readonly class ListTradesUseCase
{
    public function __construct(
        private TradeRepository $tradeRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId, ?string $status = null): TradeListResultDto
    {
        $trades = $this->tradeRepository->findByUserId($userId, $status);

        $items = [];
        foreach ($trades as $trade) {
            $items[] = self::toResultDto($trade);
        }

        return new TradeListResultDto($items);
    }

    public static function toResultDto(Trade $trade): TradeResultDto
    {
        return new TradeResultDto(
            id: $trade->getId(),
            bybitOrderId: $trade->getUfBybitOrderId(),
            bybitStatus: $trade->getUfBybitStatus(),
            side: $trade->getUfSide(),
            price: $trade->getUfPrice(),
            quantity: $trade->getUfQuantity(),
            fiatAmount: $trade->getUfFiatAmount(),
            fee: $trade->getUfFee(),
            status: $trade->getUfStatus(),
            counterpartyName: $trade->getUfCounterpartyName(),
            currencyPairId: $trade->getUfCurrencyPairId(),
            advertisementId: 0 !== $trade->getUfAdvertisementId() ? $trade->getUfAdvertisementId() : null,
            paymentDeadline: $trade->getUfPaymentDeadline()?->format('c'),
            paidAt: $trade->getUfPaidAt()?->format('c'),
            completedAt: $trade->getUfCompletedAt()?->format('c'),
            cancelledAt: $trade->getUfCancelledAt()?->format('c'),
            cancelReason: $trade->getUfCancelReason() ?: null,
            createdAt: $trade->getUfCreatedAt()?->format('c'),
            updatedAt: $trade->getUfUpdatedAt()?->format('c'),
        );
    }
}
