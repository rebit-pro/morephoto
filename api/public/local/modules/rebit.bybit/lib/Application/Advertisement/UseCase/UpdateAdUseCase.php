<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\UseCase;

use Rebit\Bybit\Application\Advertisement\Dto\Request\UpdateAdRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Result\UpdateAdResultDto;
use Rebit\Bybit\Infrastructure\Http\ByBitHttpClient;
use Rebit\Bybit\Infrastructure\Http\Exception\ByBitHttpException;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;

final readonly class UpdateAdUseCase
{
    private const string ENDPOINT = '/v5/p2p/item/update';

    public function __construct(
        private ByBitHttpClient $httpClient,
    ) {}

    /**
     * @throws ByBitHttpException
     * @throws RebitHttpClientException
     * @throws \JsonException
     */
    public function execute(UpdateAdRequestDto $dto): UpdateAdResultDto
    {
        $response = $this->httpClient->post(self::ENDPOINT, $dto->toArray());

        /** @var array{result?: array<string, mixed>} $response */
        $result = (array)($response['result'] ?? []);

        return new UpdateAdResultDto(
            securityRiskToken: (string)($result['securityRiskToken'] ?? ''),
            riskTokenType: (string)($result['riskTokenType'] ?? ''),
            riskVersion: (string)($result['riskVersion'] ?? ''),
            needSecurityRisk: (bool)($result['needSecurityRisk'] ?? false),
        );
    }
}
