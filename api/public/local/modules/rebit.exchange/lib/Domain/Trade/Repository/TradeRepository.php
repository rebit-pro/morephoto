<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Entity\TradeCollection;
use Rebit\Exchange\Domain\Trade\Entity\Table\TradeTable;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Share\Shared\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;
use Bitrix\Main\ORM\Query\Query;

final class TradeRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findByBybitOrderId(string $bybitOrderId): ?Trade
    {
        return $this->query(
            fn(): ?Trade => TradeTable::query()
                ->setSelect(['*'])
                ->where('UF_BYBIT_ORDER_ID', $bybitOrderId)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?Trade
    {
        return $this->query(
            fn(): ?Trade => TradeTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * Сделки пользователя (как buyer или seller).
     *
     * @throws RepositoryException
     */
    public function findByUserId(int $userId, ?string $status = null): TradeCollection
    {
        return $this->query(
            function() use ($userId, $status): TradeCollection {
                $query = TradeTable::query()
                    ->setSelect(['*'])
                    ->setOrder(['ID' => 'DESC'])
                ;

                $query->where(
                    Query::filter()
                        ->logic('or')
                        ->where('UF_BUYER_USER_ID', $userId)
                        ->where('UF_SELLER_USER_ID', $userId),
                );

                if (null !== $status) {
                    $query->where('UF_STATUS', $status);
                }

                return $query->exec()->fetchCollection();
            },
        );
    }

    /**
     * Активные сделки (pending_payment, payment_sent, payment_confirmed, disputed).
     *
     * @throws RepositoryException
     */
    public function findActiveByUserId(int $userId): TradeCollection
    {
        $activeStatuses = [
            TradeStatusEnum::PendingPayment->value,
            TradeStatusEnum::PaymentSent->value,
            TradeStatusEnum::PaymentConfirmed->value,
            TradeStatusEnum::Disputed->value,
        ];

        return $this->query(
            fn(): TradeCollection => TradeTable::query()
                ->setSelect(['*'])
                ->where(
                    Query::filter()
                        ->logic('or')
                        ->where('UF_BUYER_USER_ID', $userId)
                        ->where('UF_SELLER_USER_ID', $userId),
                )
                ->whereIn('UF_STATUS', $activeStatuses)
                ->setOrder(['ID' => 'DESC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function save(Trade $trade): void
    {
        $trade->setUfUpdatedAt(new DateTime());
        $this->persist($trade);
    }

    /**
     * Создание новой сделки из данных Bybit polling.
     *
     * @param array<string, mixed> $data
     *
     * @throws RepositoryException
     */
    public function createFromBybit(array $data): Trade
    {
        $now = new DateTime();

        /** @var Trade $trade */
        $trade = TradeTable::createObject()
            ->setUfBybitOrderId((string)($data['bybitOrderId'] ?? ''))
            ->setUfBybitStatus((int)($data['bybitStatus'] ?? 0))
            ->setUfBuyerUserId((int)($data['buyerUserId'] ?? 0))
            ->setUfSellerUserId((int)($data['sellerUserId'] ?? 0))
            ->setUfAdvertisementId((int)($data['advertisementId'] ?? 0))
            ->setUfOrderBookEntryId((int)($data['orderBookEntryId'] ?? 0))
            ->setUfCurrencyPairId((int)($data['currencyPairId'] ?? 0))
            ->setUfSide((string)($data['side'] ?? ''))
            ->setUfPrice((float)($data['price'] ?? 0))
            ->setUfQuantity((float)($data['quantity'] ?? 0))
            ->setUfFiatAmount((float)($data['fiatAmount'] ?? 0))
            ->setUfFee((float)($data['fee'] ?? 0))
            ->setUfPaymentMethodId((int)($data['paymentMethodId'] ?? 0))
            ->setUfStatus((string)($data['status'] ?? TradeStatusEnum::PendingPayment->value))
            ->setUfCounterpartyName((string)($data['counterpartyName'] ?? ''))
            ->setUfCreatedAt($now)
            ->setUfUpdatedAt($now)
        ;

        $this->persist($trade);

        return $trade;
    }
}
