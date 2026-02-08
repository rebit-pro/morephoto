<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\UseCase;

use Rebit\Bybit\Application\Advertisement\Dto\Request\OrderBookRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Result\AdItemDto;
use Rebit\Bybit\Application\Advertisement\Dto\Result\OrderBookResultDto;
use Rebit\Bybit\Infrastructure\Http\ByBitHttpClient;
use Rebit\Bybit\Infrastructure\Http\Exception\ByBitHttpException;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;

final readonly class OrderBookUseCase
{
    private const string ENDPOINT = '/v5/p2p/item/online';

    public function __construct(
        private ByBitHttpClient $httpClient,
    ) {}

    /**
     * @throws ByBitHttpException
     * @throws RebitHttpClientException
     * @throws \JsonException
     */
    public function execute(OrderBookRequestDto $dto): OrderBookResultDto
    {
        $response = $this->httpClient->post(self::ENDPOINT, [
            'tokenId' => $dto->tokenId,
            'currencyId' => $dto->currencyId,
            'side' => $dto->side,
            'page' => $dto->page,
            'size' => $dto->size,
        ]);

        /** @var array{result?: array{count?: int, items?: array<int, array<string, mixed>>}} $response */
        $result = $response['result'] ?? [];
        $count = (int)($result['count'] ?? 0);
        $items = $this->mapItems($result['items'] ?? []);

        return new OrderBookResultDto(
            count: $count,
            items: $items,
        );
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return AdItemDto[]
     */
    private function mapItems(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = new AdItemDto(
                id: (string)($item['id'] ?? ''),
                nickName: (string)($item['nickName'] ?? ''),
                price: (string)($item['price'] ?? ''),
                lastQuantity: (string)($item['lastQuantity'] ?? ''),
                minAmount: (string)($item['minAmount'] ?? ''),
                maxAmount: (string)($item['maxAmount'] ?? ''),
                payments: array_values(array_map('strval', (array)($item['payments'] ?? []))),
                recentOrderNum: (string)($item['recentOrderNum'] ?? ''),
                recentExecuteRate: (string)($item['recentExecuteRate'] ?? ''),
                isOnline: (bool)($item['isOnline'] ?? false),
                authTag: array_values(array_map('strval', (array)($item['authTag'] ?? []))),
                paymentPeriod: (int)($item['paymentPeriod'] ?? 0),
            );
        }

        return $result;
    }
}

