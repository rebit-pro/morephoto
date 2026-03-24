<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Advertisement\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;
use Rebit\Exchange\Domain\Advertisement\Entity\AdvertisementCollection;
use Rebit\Exchange\Domain\Advertisement\Entity\Table\AdvertisementTable;
use Rebit\Exchange\Domain\Advertisement\Enum\AdvertisementStatusEnum;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class AdvertisementRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findByUserId(int $userId, ?string $status = null): AdvertisementCollection
    {
        return $this->query(
            function() use ($userId, $status): AdvertisementCollection {
                $query = AdvertisementTable::query()
                    ->setSelect(['*'])
                    ->where('UF_USER_ID', $userId)
                    ->setOrder(['ID' => 'DESC'])
                ;

                if (null !== $status) {
                    $query->where('UF_STATUS', $status);
                }

                return $query->exec()->fetchCollection();
            },
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?Advertisement
    {
        return $this->query(
            fn(): ?Advertisement => AdvertisementTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByBybitAdId(string $bybitAdId): ?Advertisement
    {
        return $this->query(
            fn(): ?Advertisement => AdvertisementTable::query()
                ->setSelect(['*'])
                ->where('UF_BYBIT_AD_ID', $bybitAdId)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function create(
        int $userId,
        int $currencyPairId,
        string $side,
        string $priceType,
        float $price,
        ?float $premium,
        float $quantity,
        float $minAmount,
        float $maxAmount,
        string $paymentMethodIds,
        int $paymentPeriod,
        ?string $conditions,
        ?int $chatScriptId,
        AdvertisementStatusEnum $status,
        ?string $bybitAdId = null,
        ?float $feeRate = null,
    ): Advertisement {
        $now = new DateTime();

        /** @var Advertisement $ad */
        $ad = AdvertisementTable::createObject()
            ->setUfUserId($userId)
            ->setUfBybitAdId($bybitAdId ?? '')
            ->setUfCurrencyPairId($currencyPairId)
            ->setUfSide($side)
            ->setUfPriceType($priceType)
            ->setUfPrice($price)
            ->setUfPremium($premium ?? 0.0)
            ->setUfQuantity($quantity)
            ->setUfQuantityRemaining($quantity)
            ->setUfMinAmount($minAmount)
            ->setUfMaxAmount($maxAmount)
            ->setUfPaymentMethodIds($paymentMethodIds)
            ->setUfPaymentPeriod($paymentPeriod)
            ->setUfFeeRate($feeRate ?? 0.0)
            ->setUfConditions($conditions ?? '')
            ->setUfChatScriptId($chatScriptId ?? 0)
            ->setUfStatus($status->value)
            ->setUfCreatedAt($now)
            ->setUfUpdatedAt($now)
        ;

        $this->persist($ad);

        return $ad;
    }

    /**
     * @throws RepositoryException
     */
    public function save(Advertisement $ad): void
    {
        $ad->setUfUpdatedAt(new DateTime());
        $this->persist($ad);
    }

    /**
     * Обнуляет UF_CHAT_SCRIPT_ID у всех объявлений, использующих указанный скрипт.
     *
     * @throws RepositoryException
     */
    public function clearChatScriptId(int $chatScriptId): void
    {
        $ads = $this->query(
            fn(): AdvertisementCollection => AdvertisementTable::query()
                ->setSelect(['ID', 'UF_CHAT_SCRIPT_ID', 'UF_UPDATED_AT'])
                ->where('UF_CHAT_SCRIPT_ID', $chatScriptId)
                ->exec()
                ->fetchCollection(),
        );

        $now = new DateTime();
        foreach ($ads as $ad) {
            $ad->setUfChatScriptId(0)->setUfUpdatedAt($now);
            $this->persist($ad);
        }
    }
}
