<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitAdvertisementCreateResultDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitAdvertisementInfoDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitAdvertisementItemDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitAdvertisementListDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitCreateAdvertisementDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitPersonalAdvertisementListRequestDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitTradingPreferenceSetDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitUpdateAdvertisementDto;
use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Адаптер для управления объявлениями через Bybit P2P API.
 */
final readonly class BybitAdvertisementGateway implements BybitAdvertisementGatewayInterface
{
    private const string CREATE_ENDPOINT = '/v5/p2p/item/create';
    private const string UPDATE_ENDPOINT = '/v5/p2p/item/update';
    private const string CANCEL_ENDPOINT = '/v5/p2p/item/cancel';
    private const string PERSONAL_LIST_ENDPOINT = '/v5/p2p/item/personal/list';
    private const string INFO_ENDPOINT = '/v5/p2p/item/info';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    public function create(int $userId, BybitCreateAdvertisementDto $dto): BybitAdvertisementCreateResultDto
    {
        $response = $this->post($userId, self::CREATE_ENDPOINT, [
            'tokenId' => $dto->tokenId,
            'currencyId' => $dto->currencyId,
            'side' => $dto->side,
            'priceType' => $dto->priceType,
            'premium' => $dto->premium,
            'price' => $dto->price,
            'minAmount' => $dto->minAmount,
            'maxAmount' => $dto->maxAmount,
            'paymentIds' => $dto->paymentIds,
            'remark' => $dto->remark,
            'tradingPreferenceSet' => $this->normalizeTradingPreferenceSet($dto->tradingPreferenceSet),
            'quantity' => $dto->quantity,
            'paymentPeriod' => $dto->paymentPeriod,
            'itemType' => $dto->itemType,
        ]);

        return new BybitAdvertisementCreateResultDto(
            itemId: (string)($response['itemId'] ?? ''),
        );
    }

    public function update(int $userId, BybitUpdateAdvertisementDto $dto): void
    {
        $this->post($userId, self::UPDATE_ENDPOINT, [
            'itemId' => $dto->itemId,
            'price' => $dto->price,
            'premium' => $dto->premium,
            'minAmount' => $dto->minAmount,
            'maxAmount' => $dto->maxAmount,
            'paymentIds' => $dto->paymentIds,
            'remark' => $dto->remark,
            'tradingPreferenceSet' => $this->normalizeTradingPreferenceSet($dto->tradingPreferenceSet),
            'quantity' => $dto->quantity,
            'paymentPeriod' => $dto->paymentPeriod,
        ]);
    }

    public function cancel(int $userId, string $bybitAdId): void
    {
        $this->post($userId, self::CANCEL_ENDPOINT, ['itemId' => $bybitAdId]);
    }

    public function fetchPersonalList(
        int $userId,
        BybitPersonalAdvertisementListRequestDto $dto = new BybitPersonalAdvertisementListRequestDto(),
    ): BybitAdvertisementListDto {
        return $this->mapAdvertisementList(
            $this->post($userId, self::PERSONAL_LIST_ENDPOINT, $this->normalizePersonalListParams($dto)),
        );
    }

    public function fetchInfo(int $userId, string $bybitAdId): BybitAdvertisementInfoDto
    {
        return $this->mapAdvertisementInfo(
            $this->post($userId, self::INFO_ENDPOINT, ['itemId' => $bybitAdId]),
        );
    }

    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    private function post(int $userId, string $endpoint, array $body): array
    {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->post(
                $endpoint,
                $connection->credentials,
                $connection->environment,
                $body,
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                "Bybit API error [{$endpoint}]: " . $e->getMessage(),
                502,
            );
        }

        return $response->result;
    }

    /**
     * @return array<string, string>
     */
    private function normalizeTradingPreferenceSet(BybitTradingPreferenceSetDto $dto): array
    {
        $data = [
            'hasUnPostAd' => $dto->hasUnPostAd,
            'isKyc' => $dto->isKyc,
            'isEmail' => $dto->isEmail,
            'isMobile' => $dto->isMobile,
            'hasRegisterTime' => $dto->hasRegisterTime,
            'registerTimeThreshold' => $dto->registerTimeThreshold,
            'orderFinishNumberDay30' => $dto->orderFinishNumberDay30,
            'completeRateDay30' => $dto->completeRateDay30,
            'nationalLimit' => $dto->nationalLimit,
            'hasOrderFinishNumberDay30' => $dto->hasOrderFinishNumberDay30,
            'hasCompleteRateDay30' => $dto->hasCompleteRateDay30,
            'hasNationalLimit' => $dto->hasNationalLimit,
        ];

        return array_filter(
            $data,
            static fn(string $value): bool => '' !== $value,
        );
    }

    /**
     * @return array<string, string>
     */
    private function normalizePersonalListParams(BybitPersonalAdvertisementListRequestDto $dto): array
    {
        $params = [
            'page' => $dto->page,
            'size' => $dto->size,
        ];

        foreach ([
            'itemId' => $dto->itemId,
            'status' => $dto->status,
            'side' => $dto->side,
            'tokenId' => $dto->tokenId,
            'currencyId' => $dto->currencyId,
        ] as $key => $value) {
            if ('' !== $value) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * @param array<string, mixed> $result
     */
    private function mapAdvertisementList(array $result): BybitAdvertisementListDto
    {
        /** @var array<int, array<string, mixed>> $items */
        $items = is_array($result['items'] ?? null)
            ? $result['items']
            : [];

        return new BybitAdvertisementListDto(
            count: (int)($result['count'] ?? count($items)),
            items: array_map(fn(array $item): BybitAdvertisementItemDto => $this->mapAdvertisementItem($item), $items),
        );
    }

    /**
     * @param array<string, mixed> $item
     */
    private function mapAdvertisementItem(array $item): BybitAdvertisementItemDto
    {
        return new BybitAdvertisementItemDto(
            id: (string)($item['id'] ?? ''),
            userId: (string)($item['userId'] ?? ''),
            nickName: (string)($item['nickName'] ?? ''),
            tokenId: (string)($item['tokenId'] ?? ''),
            currencyId: (string)($item['currencyId'] ?? ''),
            side: (int)($item['side'] ?? 0),
            priceType: (int)($item['priceType'] ?? 0),
            price: (string)($item['price'] ?? ''),
            premium: (string)($item['premium'] ?? ''),
            lastQuantity: (string)($item['lastQuantity'] ?? ''),
            quantity: (string)($item['quantity'] ?? ''),
            frozenQuantity: (string)($item['frozenQuantity'] ?? ''),
            executedQuantity: (string)($item['executedQuantity'] ?? ''),
            minAmount: (string)($item['minAmount'] ?? ''),
            maxAmount: (string)($item['maxAmount'] ?? ''),
            remark: (string)($item['remark'] ?? ''),
            status: (int)($item['status'] ?? 0),
            createDate: (string)($item['createDate'] ?? ''),
            payments: $this->normalizeStringList($item['payments'] ?? []),
            hiddenReason: (string)($item['hiddenReason'] ?? ''),
            tradingPreferenceSet: $this->mapTradingPreferenceSet($item['tradingPreferenceSet'] ?? []),
            updateDate: (string)($item['updateDate'] ?? ''),
            feeRate: (string)($item['feeRate'] ?? ''),
            paymentPeriod: (int)($item['paymentPeriod'] ?? 0),
            itemType: (string)($item['itemType'] ?? ''),
        );
    }

    /**
     * @param array<string, mixed> $result
     */
    private function mapAdvertisementInfo(array $result): BybitAdvertisementInfoDto
    {
        return new BybitAdvertisementInfoDto(
            id: (string)($result['id'] ?? ''),
            accountId: (string)($result['accountId'] ?? ''),
            userId: (string)($result['userId'] ?? ''),
            nickName: (string)($result['nickName'] ?? ''),
            tokenId: (string)($result['tokenId'] ?? ''),
            currencyId: (string)($result['currencyId'] ?? ''),
            side: (int)($result['side'] ?? 0),
            priceType: (int)($result['priceType'] ?? 0),
            price: (string)($result['price'] ?? ''),
            premium: (string)($result['premium'] ?? ''),
            lastQuantity: (string)($result['lastQuantity'] ?? ''),
            quantity: (string)($result['quantity'] ?? ''),
            frozenQuantity: (string)($result['frozenQuantity'] ?? ''),
            executedQuantity: (string)($result['executedQuantity'] ?? ''),
            minAmount: (string)($result['minAmount'] ?? ''),
            maxAmount: (string)($result['maxAmount'] ?? ''),
            remark: (string)($result['remark'] ?? ''),
            hiddenReason: (string)($result['hiddenReason'] ?? ''),
            status: (int)($result['status'] ?? 0),
            createDate: (string)($result['createDate'] ?? ''),
            payments: $this->normalizeStringList($result['payments'] ?? []),
            tradingPreferenceSet: $this->mapTradingPreferenceSet($result['tradingPreferenceSet'] ?? []),
            updateDate: (string)($result['updateDate'] ?? ''),
            feeRate: (string)($result['feeRate'] ?? ''),
            version: (int)($result['version'] ?? 0),
            paymentPeriod: (int)($result['paymentPeriod'] ?? 0),
            itemType: (string)($result['itemType'] ?? ''),
        );
    }

    private function mapTradingPreferenceSet(mixed $data): BybitTradingPreferenceSetDto
    {
        if (!is_array($data)) {
            return new BybitTradingPreferenceSetDto();
        }

        return new BybitTradingPreferenceSetDto(
            hasUnPostAd: (string)($data['hasUnPostAd'] ?? ''),
            isKyc: (string)($data['isKyc'] ?? ''),
            isEmail: (string)($data['isEmail'] ?? ''),
            isMobile: (string)($data['isMobile'] ?? ''),
            hasRegisterTime: (string)($data['hasRegisterTime'] ?? ''),
            registerTimeThreshold: (string)($data['registerTimeThreshold'] ?? ''),
            orderFinishNumberDay30: (string)($data['orderFinishNumberDay30'] ?? ''),
            completeRateDay30: (string)($data['completeRateDay30'] ?? ''),
            nationalLimit: (string)($data['nationalLimit'] ?? ''),
            hasOrderFinishNumberDay30: (string)($data['hasOrderFinishNumberDay30'] ?? ''),
            hasCompleteRateDay30: (string)($data['hasCompleteRateDay30'] ?? ''),
            hasNationalLimit: (string)($data['hasNationalLimit'] ?? ''),
        );
    }

    /**
     * @return list<string>
     */
    private function normalizeStringList(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        return array_values(array_map(static fn(mixed $item): string => (string)$item, $items));
    }
}
