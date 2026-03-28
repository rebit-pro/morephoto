<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\UseCase;

use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitCreateAdvertisementDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitTradingPreferenceSetDto;
use Rebit\Exchange\Application\Advertisement\Dto\Request\ToggleAdvertisementRequestDto;
use Rebit\Exchange\Application\Advertisement\Dto\Result\AdvertisementResultDto;
use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;
use Rebit\Exchange\Domain\Advertisement\Enum\AdvertisementStatusEnum;
use Rebit\Exchange\Domain\Advertisement\Enum\PriceTypeEnum;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\Shared\Enum\SideEnum;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Переключение статуса объявления: active ↔ paused.
 *
 * При паузе — отменяем на Bybit (cancel).
 * При активации — пересоздаём на Bybit (create).
 */
final readonly class ToggleAdvertisementUseCase
{
    public function __construct(
        private AdvertisementRepository $advertisementRepository,
        private CurrencyPairRepository $currencyPairRepository,
        private BybitAdvertisementGatewayInterface $bybitGateway,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     * @throws \JsonException
     */
    public function execute(
        int $advertisementId,
        int $userId,
        ToggleAdvertisementRequestDto $dto,
    ): AdvertisementResultDto {
        $ad = $this->advertisementRepository->findById($advertisementId);

        if (null === $ad) {
            throw new EntityNotFoundException('Объявление не найдено');
        }

        if ($ad->getUfUserId() !== $userId) {
            throw new HttpException('Нет доступа к этому объявлению', 403);
        }

        $currentStatus = AdvertisementStatusEnum::from($ad->getUfStatus());
        $targetStatus = AdvertisementStatusEnum::from($dto->status);

        $this->validateTransition($currentStatus, $targetStatus);

        $bybitAdId = $ad->getUfBybitAdId();

        if (AdvertisementStatusEnum::Paused === $targetStatus && '' !== $bybitAdId) {
            $this->bybitGateway->cancel($userId, $bybitAdId);
            $ad->setUfBybitAdId('');
        }

        if (AdvertisementStatusEnum::Active === $targetStatus) {
            $this->reactivateOnBybit($ad, $userId);
        }

        $ad->setUfStatus($targetStatus->value);
        $this->advertisementRepository->save($ad);

        return $this->toResultDto($ad);
    }

    /**
     * @throws ValidationHttpException
     */
    private function validateTransition(
        AdvertisementStatusEnum $current,
        AdvertisementStatusEnum $target,
    ): void {
        $allowed = match ($current) {
            AdvertisementStatusEnum::Active => [AdvertisementStatusEnum::Paused],
            AdvertisementStatusEnum::Paused => [AdvertisementStatusEnum::Active],
            default => [],
        };

        if (!in_array($target, $allowed, true)) {
            throw new ValidationHttpException(
                "Переход из статуса «{$current->value}» в «{$target->value}» невозможен",
            );
        }
    }

    /**
     * Пересоздание объявления на Bybit при активации из паузы.
     *
     * @throws HttpException
     * @throws \JsonException
     */
    private function reactivateOnBybit(Advertisement $ad, int $userId): void
    {
        $pair = $this->currencyPairRepository->findById($ad->getUfCurrencyPairId());

        if (null === $pair) {
            throw new ValidationHttpException('Валютная пара объявления не найдена');
        }

        $parts = explode('_', $pair->getUfCode());
        if (2 !== count($parts)) {
            throw new ValidationHttpException('Некорректный формат кода валютной пары');
        }

        [$tokenId, $currencyId] = $parts;

        /** @var array<int, string> $paymentIds */
        $paymentIds = json_decode($ad->getUfPaymentMethodIds(), true, flags: JSON_THROW_ON_ERROR);

        $side = SideEnum::from($ad->getUfSide());
        $priceType = PriceTypeEnum::from($ad->getUfPriceType());

        $bybitAdId = $this->bybitGateway->create(
            $userId,
            new BybitCreateAdvertisementDto(
                tokenId: $tokenId,
                currencyId: $currencyId,
                side: $side->toBybit(),
                priceType: $priceType->toBybit(),
                premium: 0.0 !== $ad->getUfPremium() ? (string)$ad->getUfPremium() : '',
                price: (string)$ad->getUfPrice(),
                minAmount: (string)$ad->getUfMinAmount(),
                maxAmount: (string)$ad->getUfMaxAmount(),
                paymentIds: $paymentIds,
                remark: $ad->getUfConditions(),
                tradingPreferenceSet: new BybitTradingPreferenceSetDto(),
                quantity: (string)$ad->getUfQuantityRemaining(),
                paymentPeriod: (string)$ad->getUfPaymentPeriod(),
                itemType: 'ORIGIN',
            ),
        )->itemId;

        $ad->setUfBybitAdId($bybitAdId);
    }

    /**
     * @throws \JsonException
     */
    private function toResultDto(Advertisement $ad): AdvertisementResultDto
    {
        /** @var array<int, string> $paymentMethodIds */
        $paymentMethodIds = json_decode($ad->getUfPaymentMethodIds(), true, flags: JSON_THROW_ON_ERROR);

        return new AdvertisementResultDto(
            id: (int)$ad->getId(),
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
            paymentMethodIds: $paymentMethodIds,
            paymentPeriod: $ad->getUfPaymentPeriod(),
            feeRate: $ad->getUfFeeRate(),
            conditions: $ad->getUfConditions(),
            chatScriptId: 0 !== $ad->getUfChatScriptId() ? $ad->getUfChatScriptId() : null,
            status: $ad->getUfStatus(),
            createdAt: $ad->getUfCreatedAt()?->format('c'),
            updatedAt: $ad->getUfUpdatedAt()?->format('c'),
        );
    }
}
