<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Mapper;

use Rebit\Exchange\Application\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Domain\Trade\Entity\Trade;

/**
 * Маппер Trade Entity → TradeResultDto.
 */
final class TradeResultDtoMapper
{
    public static function fromEntity(Trade $trade): TradeResultDto
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
