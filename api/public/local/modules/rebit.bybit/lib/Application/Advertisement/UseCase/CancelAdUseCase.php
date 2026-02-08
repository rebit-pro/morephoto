<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\UseCase;

use Rebit\Bybit\Application\Advertisement\Dto\Request\CancelAdRequestDto;
use Rebit\Bybit\Infrastructure\Http\ByBitHttpClient;
use Rebit\Bybit\Infrastructure\Http\Exception\ByBitHttpException;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;

final readonly class CancelAdUseCase
{
    private const string ENDPOINT = '/v5/p2p/item/cancel';

    public function __construct(
        private ByBitHttpClient $httpClient,
    ) {}

    /**
     * @throws ByBitHttpException
     * @throws RebitHttpClientException
     * @throws \JsonException
     */
    public function execute(CancelAdRequestDto $dto): void
    {
        $this->httpClient->post(self::ENDPOINT, $dto->toArray());
    }
}

