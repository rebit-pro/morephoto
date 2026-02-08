<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http;

use Rebit\Bybit\Infrastructure\Http\Config\ByBitConfig;
use Rebit\Bybit\Infrastructure\Http\Contract\RequestIdGeneratorInterface;
use Rebit\Bybit\Infrastructure\Http\Exception\ByBitHttpException;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClient;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;

final class ByBitHttpClient
{
    private const string HEADER_API_KEY = 'X-BAPI-API-KEY';
    private const string HEADER_TIMESTAMP = 'X-BAPI-TIMESTAMP';
    private const string HEADER_SIGN = 'X-BAPI-SIGN';
    private const string HEADER_RECV_WINDOW = 'X-BAPI-RECV-WINDOW';
    private const string HEADER_REQUEST_ID = 'cdn-request-id';

    public function __construct(
        private readonly ByBitConfig $config,
        private readonly RebitHttpClient $httpClient,
        private readonly RequestIdGeneratorInterface $requestIdGenerator,
    ) {}

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     *
     * @throws ByBitHttpException|RebitHttpClientException
     */
    public function get(string $endpoint, array $params = []): array
    {
        $queryString = http_build_query($params);
        $url = $this->buildUrl($endpoint, $queryString);
        $headers = $this->buildHeaders($queryString);

        $response = $this->httpClient->get($url, $headers);

        return $this->validateResponse($response);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     *
     * @throws ByBitHttpException
     * @throws \JsonException|RebitHttpClientException
     */
    public function post(string $endpoint, array $data = []): array
    {
        $url = $this->buildUrl($endpoint);
        $jsonBody = json_encode($data, JSON_THROW_ON_ERROR);
        $headers = $this->buildHeaders($jsonBody);
        $headers['Content-Type'] = 'application/json';

        $response = $this->httpClient->post($url, $data, $headers);

        return $this->validateResponse($response);
    }

    /**
     * @param array<string, mixed> $response
     *
     * @return array<string, mixed>
     *
     * @throws ByBitHttpException
     */
    private function validateResponse(array $response): array
    {
        $retCode = $response['retCode'] ?? -1;

        if (0 !== $retCode) {
            throw new ByBitHttpException(
                sprintf(
                    'ByBit API error [%d]: %s',
                    $retCode,
                    $response['retMsg'] ?? 'Unknown error',
                ),
                $retCode,
            );
        }

        return $response;
    }

    private function buildUrl(string $endpoint, string $queryString = ''): string
    {
        $url = rtrim($this->config->getBaseUrl(), '/') . '/' . ltrim($endpoint, '/');

        if ('' !== $queryString) {
            $url .= '?' . $queryString;
        }

        return $url;
    }

    /**
     * @return array<string, string>
     */
    private function buildHeaders(string $payload): array
    {
        $timestamp = $this->getTimestamp();
        $signature = $this->generateSignature($timestamp, $payload);

        return [
            self::HEADER_API_KEY => $this->config->getApiKey(),
            self::HEADER_TIMESTAMP => $timestamp,
            self::HEADER_SIGN => $signature,
            self::HEADER_RECV_WINDOW => (string)$this->config->getRecvWindow(),
            self::HEADER_REQUEST_ID => $this->requestIdGenerator->generate(),
        ];
    }

    private function generateSignature(string $timestamp, string $payload): string
    {
        $signString = $timestamp
            . $this->config->getApiKey()
            . $this->config->getRecvWindow()
            . $payload;

        return hash_hmac('sha256', $signString, $this->config->getApiSecret());
    }

    private function getTimestamp(): string
    {
        return (string)(int)(microtime(true) * 1000);
    }
}
