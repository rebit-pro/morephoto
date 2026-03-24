<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\UseCase;

use Rebit\Exchange\Application\Advertisement\Dto\Result\AdvertisementListResultDto;
use Rebit\Exchange\Application\Advertisement\Dto\Result\AdvertisementResultDto;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение списка объявлений текущего пользователя.
 */
final readonly class ListAdvertisementsUseCase
{
    public function __construct(
        private AdvertisementRepository $advertisementRepository,
    ) {}

    /**
     * @throws RepositoryException
     * @throws \JsonException
     */
    public function execute(int $userId, ?string $status = null): AdvertisementListResultDto
    {
        $ads = $this->advertisementRepository->findByUserId($userId, $status);

        $items = [];
        foreach ($ads as $ad) {
            $paymentIds = json_decode($ad->getUfPaymentMethodIds() ?: '[]', true, 512, JSON_THROW_ON_ERROR);

            $items[] = new AdvertisementResultDto(
                id: $ad->getId(),
                bybitAdId: $ad->getUfBybitAdId(),
                currencyPairId: $ad->getUfCurrencyPairId(),
                side: $ad->getUfSide(),
                priceType: $ad->getUfPriceType(),
                price: $ad->getUfPrice(),
                premium: $ad->getUfPremium(),
                quantity: $ad->getUfQuantity(),
                quantityRemaining: $ad->getUfQuantityRemaining(),
                minAmount: $ad->getUfMinAmount(),
                maxAmount: $ad->getUfMaxAmount(),
                paymentMethodIds: is_array($paymentIds) ? $paymentIds : [],
                paymentPeriod: $ad->getUfPaymentPeriod(),
                feeRate: $ad->getUfFeeRate(),
                conditions: $ad->getUfConditions() ?? '',
                chatScriptId: 0 !== $ad->getUfChatScriptId() ? $ad->getUfChatScriptId() : null,
                status: $ad->getUfStatus(),
                createdAt: $ad->getUfCreatedAt()?->format('c'),
                updatedAt: $ad->getUfUpdatedAt()?->format('c'),
            );
        }

        return new AdvertisementListResultDto($items);
    }
}
