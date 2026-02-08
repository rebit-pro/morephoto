<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\UseCase;

use Rebit\Bybit\Application\Advertisement\Dto\Request\AdInfoRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Result\AdInfoResultDto;
use Rebit\Bybit\Infrastructure\Http\ByBitHttpClient;
use Rebit\Bybit\Infrastructure\Http\Exception\ByBitHttpException;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;

final readonly class AdInfoUseCase
{
    private const string ENDPOINT = '/v5/p2p/item/info';

    public function __construct(
        private ByBitHttpClient $httpClient,
    ) {}

    /**
     * @throws ByBitHttpException
     * @throws RebitHttpClientException
     * @throws \JsonException
     */
    public function execute(AdInfoRequestDto $dto): AdInfoResultDto
    {
        $response = $this->httpClient->post(self::ENDPOINT, $dto->toArray());

        /** @var array{result?: array<string, mixed>} $response */
        $result = (array)($response['result'] ?? []);

        return new AdInfoResultDto(
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
            status: (int)($result['status'] ?? 0),
            createDate: (string)($result['createDate'] ?? ''),
            payments: array_values(array_map('strval', (array)($result['payments'] ?? []))),
            updateDate: (string)($result['updateDate'] ?? ''),
            feeRate: (string)($result['feeRate'] ?? ''),
            version: (int)($result['version'] ?? 0),
            paymentPeriod: (int)($result['paymentPeriod'] ?? 0),
            itemType: (string)($result['itemType'] ?? ''),
        );
    }
}

