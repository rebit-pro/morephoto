<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\UseCase;

use Rebit\Exchange\Application\Advertisement\Dto\Request\CreateAdvertisementRequestDto;
use Rebit\Exchange\Application\Advertisement\Dto\Result\AdvertisementResultDto;
use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Exchange\Domain\Advertisement\Enum\AdvertisementStatusEnum;
use Rebit\Exchange\Domain\Advertisement\Enum\PriceTypeEnum;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\Shared\Enum\SideEnum;
use Rebit\Share\Application\Contract\Wallet\BalanceQueryInterface;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;

/**
 * Создание P2P-объявления через Bybit API + локальное сохранение.
 */
final readonly class CreateAdvertisementUseCase
{
    public function __construct(
        private AdvertisementRepository $advertisementRepository,
        private CurrencyPairRepository $currencyPairRepository,
        private BybitAdvertisementGatewayInterface $bybitGateway,
        private BalanceQueryInterface $balanceQuery,
    ) {}

    /**
     * @throws HttpException
     * @throws \JsonException|RepositoryException
     */
    public function execute(CreateAdvertisementRequestDto $dto, int $userId): AdvertisementResultDto
    {
        $pair = $this->currencyPairRepository->findById($dto->currencyPairId);
        if (null === $pair) {
            throw new EntityNotFoundException('Валютная пара не найдена');
        }

        $side = SideEnum::from($dto->side);
        $priceType = PriceTypeEnum::from($dto->priceType);

        $parts = explode('_', $pair->getUfCode());
        if (2 !== count($parts)) {
            throw new ValidationHttpException('Некорректный формат кода валютной пары');
        }

        [$tokenId, $currencyId] = $parts;

        if (SideEnum::Sell === $side) {
            $tokenCurrencyId = $pair->getUfTokenCurrencyId();
            $quantity = (float)$dto->quantity;

            if (!$this->balanceQuery->hasAvailableBalance($userId, $tokenCurrencyId, $quantity)) {
                throw new ValidationHttpException('Недостаточно средств для создания объявления на продажу');
            }
        }

        $bybitAdId = $this->bybitGateway->create($userId, [
            'tokenId' => $tokenId,
            'currencyId' => $currencyId,
            'side' => $side->toBybit(),
            'priceType' => $priceType->toBybit(),
            'premium' => $dto->premium ?? '',
            'price' => $dto->price,
            'minAmount' => $dto->minAmount,
            'maxAmount' => $dto->maxAmount,
            'paymentIds' => $dto->paymentMethodIds,
            'remark' => $dto->conditions,
            'tradingPreferenceSet' => $dto->tradingPreferenceSet,
            'quantity' => $dto->quantity,
            'paymentPeriod' => (string)$dto->paymentPeriod,
            'itemType' => 'ORIGIN',
        ]);

        $ad = $this->advertisementRepository->create(
            userId: $userId,
            currencyPairId: $dto->currencyPairId,
            side: $side->value,
            priceType: $priceType->value,
            price: (float)$dto->price,
            premium: null !== $dto->premium ? (float)$dto->premium : null,
            quantity: (float)$dto->quantity,
            minAmount: (float)$dto->minAmount,
            maxAmount: (float)$dto->maxAmount,
            paymentMethodIds: json_encode($dto->paymentMethodIds, JSON_THROW_ON_ERROR),
            paymentPeriod: $dto->paymentPeriod,
            conditions: $dto->conditions,
            chatScriptId: $dto->chatScriptId,
            status: AdvertisementStatusEnum::Active,
            bybitAdId: $bybitAdId,
        );

        return $this->toResultDto($ad);
    }

    private function toResultDto(Advertisement $ad): AdvertisementResultDto
    {
        $paymentIds = json_decode($ad->getUfPaymentMethodIds() ?: '[]', true);

        return new AdvertisementResultDto(
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
}
