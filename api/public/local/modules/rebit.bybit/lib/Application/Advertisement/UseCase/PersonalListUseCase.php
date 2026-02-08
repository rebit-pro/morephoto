<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\UseCase;

use Rebit\Bybit\Application\Advertisement\Dto\Request\PersonalListRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Result\PersonalAdItemDto;
use Rebit\Bybit\Application\Advertisement\Dto\Result\PersonalListResultDto;
use Rebit\Bybit\Infrastructure\Http\ByBitHttpClient;
use Rebit\Bybit\Infrastructure\Http\Exception\ByBitHttpException;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;

final readonly class PersonalListUseCase
{
    private const string ENDPOINT = '/v5/p2p/item/personal/list';

    public function __construct(
        private ByBitHttpClient $httpClient,
    ) {}

    /**
     * @throws ByBitHttpException
     * @throws RebitHttpClientException
     * @throws \JsonException
     */
    public function execute(PersonalListRequestDto $dto): PersonalListResultDto
    {
        $response = $this->httpClient->post(self::ENDPOINT, $dto->toArray());

        /** @var array{result?: array{count?: int, hiddenFlag?: bool, items?: array<int, array<string, mixed>>}} $response */
        $result = (array)($response['result'] ?? []);

        $count = (int)($result['count'] ?? 0);
        $hiddenFlag = (bool)($result['hiddenFlag'] ?? false);
        $items = $this->mapItems((array)($result['items'] ?? []));

        return new PersonalListResultDto(
            count: $count,
            hiddenFlag: $hiddenFlag,
            items: $items,
        );
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return PersonalAdItemDto[]
     */
    private function mapItems(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = new PersonalAdItemDto(
                id: (string)($item['id'] ?? ''),
                accountId: (string)($item['accountId'] ?? ''),
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
                payments: array_values(array_map('strval', (array)($item['payments'] ?? []))),
                paymentPeriod: (int)($item['paymentPeriod'] ?? 0),
                itemType: (string)($item['itemType'] ?? ''),
                updateDate: (string)($item['updateDate'] ?? ''),
            );
        }

        return $result;
    }
}

