<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Client;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Web\Json;
use Psr\Log\LoggerInterface;
use Rebit\Bybit\Infrastructure\Auth\HmacSignatureGenerator;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitCredentials;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;
use Rebit\Share\Application\Contract\Bybit\BybitResponseDto;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClient;
use Rebit\Share\Shared\Enum\HttpMethodEnum;
use Rebit\Share\Shared\Helper\ArrayToDtoMapper;

final readonly class BybitApiClient implements BybitClientInterface
{
    private const string DEFAULT_RECV_WINDOW = '5000';

    public function __construct(
        private RebitHttpClient $httpClient,
        private HmacSignatureGenerator $signatureGenerator,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param array<string, string> $queryParams
     *
     * @throws BybitApiException
     */
    public function get(
        string $endpoint,
        BybitCredentials $credentials,
        BybitEnvironmentEnum $environment,
        array $queryParams = [],
    ): BybitResponseDto {
        $queryString = $this->buildQueryString($queryParams);
        $url = $environment->baseUrl() . $endpoint;
        if ('' !== $queryString) {
            $url .= '?' . $queryString;
        }
        $headers = $this->buildAuthHeaders($credentials, self::DEFAULT_RECV_WINDOW, $queryString);

        return $this->executeRequest(HttpMethodEnum::GET, $url, $headers);
    }

    /**
     * @param array<string, mixed> $body
     *
     * @throws ArgumentException|BybitApiException|\JsonException
     */
    public function post(
        string $endpoint,
        BybitCredentials $credentials,
        BybitEnvironmentEnum $environment,
        array $body = [],
    ): BybitResponseDto {
        $jsonBody = [] !== $body ? Json::encode($body) : '';
        $url = $environment->baseUrl() . $endpoint;
        $headers = $this->buildAuthHeaders($credentials, self::DEFAULT_RECV_WINDOW, $jsonBody);
        $headers['Content-Type'] = 'application/json';

        return $this->executeRequest(HttpMethodEnum::POST, $url, $headers, $body);
    }

    /**
     * @param array<string, string> $fields
     * @param array<string, array{
     *     path: string,
     *     name: string,
     *     mimeType: string,
     * }> $files
     *
     * @throws BybitApiException|ValidationHttpException
     */
    public function postMultipart(
        string $endpoint,
        BybitCredentials $credentials,
        BybitEnvironmentEnum $environment,
        array $fields = [],
        array $files = [],
    ): BybitResponseDto {
        $url = $environment->baseUrl() . $endpoint;
        $headers = $this->buildAuthHeaders($credentials, self::DEFAULT_RECV_WINDOW, '');

        try {
            $rawResponse = $this->httpClient->postMultipart($url, $fields, $files, $headers);
        } catch (\Exception $e) {
            $this->logger->error('Bybit API multipart request failed', [
                'method' => HttpMethodEnum::POST->value,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw new BybitApiException(
                message: sprintf('Bybit API request failed: %s', $e->getMessage()),
                previous: $e,
            );
        }

        $response = ArrayToDtoMapper::map($this->normalizeResponse($rawResponse), BybitResponseDto::class);

        if (0 !== $response->retCode) {
            $this->logger->warning('Bybit API returned error', [
                'method' => HttpMethodEnum::POST->value,
                'url' => $url,
                'retCode' => $response->retCode,
                'retMsg' => $response->retMsg,
            ]);

            throw new BybitApiException(
                message: sprintf('Bybit API error [%d]: %s', $response->retCode, $response->retMsg),
                bybitRetCode: $response->retCode,
            );
        }

        return $response;
    }

    /**
     * @return array<string, string>
     */
    private function buildAuthHeaders(
        BybitCredentials $credentials,
        string $recvWindow,
        string $payload,
    ): array {
        $timestamp = (string)$this->currentTimestampMs();
        $signature = $this->signatureGenerator->generate(
            apiSecret: $credentials->apiSecret,
            timestamp: $timestamp,
            apiKey: $credentials->apiKey,
            recvWindow: $recvWindow,
            payload: $payload,
        );

        return [
            'X-BAPI-API-KEY' => $credentials->apiKey,
            'X-BAPI-TIMESTAMP' => $timestamp,
            'X-BAPI-SIGN' => $signature,
            'X-BAPI-RECV-WINDOW' => $recvWindow,
        ];
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, mixed>  $body
     *
     * @throws BybitApiException
     */
    private function executeRequest(
        HttpMethodEnum $method,
        string $url,
        array $headers,
        array $body = [],
    ): BybitResponseDto {
        try {
            $rawResponse = HttpMethodEnum::GET === $method
                ? $this->httpClient->get($url, $headers)
                : $this->httpClient->post($url, $body, $headers);
        } catch (\Exception $e) {
            $this->logger->error('Bybit API request failed', [
                'method' => $method->value,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new BybitApiException(
                message: sprintf('Bybit API request failed: %s', $e->getMessage()),
                previous: $e,
            );
        }
        $response = ArrayToDtoMapper::map($this->normalizeResponse($rawResponse), BybitResponseDto::class);
        if (0 !== $response->retCode) {
            $this->logger->warning('Bybit API returned error', [
                'method' => $method->value,
                'url' => $url,
                'retCode' => $response->retCode,
                'retMsg' => $response->retMsg,
            ]);
            throw new BybitApiException(
                message: sprintf('Bybit API error [%d]: %s', $response->retCode, $response->retMsg),
                bybitRetCode: $response->retCode,
            );
        }

        return $response;
    }

    /**
     * Нормализует ответ Bybit API к единому формату DTO.
     *
     * P2P API использует старый формат: ret_code, ret_msg, ext_info, time_now.
     * V5 API использует новый формат: retCode, retMsg, retExtInfo, time.
     *
     * @param array<string, mixed> $rawResponse
     *
     * @return array<string, mixed>
     */
    private function normalizeResponse(array $rawResponse): array
    {
        if (!array_key_exists('ret_code', $rawResponse)) {
            return $rawResponse;
        }

        return [
            'retCode' => (int)($rawResponse['ret_code'] ?? 0),
            'retMsg' => (string)($rawResponse['ret_msg'] ?? ''),
            'result' => $rawResponse['result'] ?? [],
            'retExtInfo' => $rawResponse['ext_info'] ?? [],
            'time' => isset($rawResponse['time_now'])
                ? (int)round((float)$rawResponse['time_now'] * 1000)
                : 0,
        ];
    }

    /**
     * @param array<string, string> $params
     */
    private function buildQueryString(array $params): string
    {
        if ([] === $params) {
            return '';
        }

        return http_build_query($params);
    }

    private function currentTimestampMs(): int
    {
        return (int)round(microtime(true) * 1000);
    }
}
