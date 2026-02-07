<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Http;

use Rebit\Bybit\Infrastructure\Http\Config\ByBitConfig;

final class ByBitHttpClient implements ByBitHttpClientInterface
{
    private const HEADER_API_KEY = 'X-BAPI-API-KEY';
    private const HEADER_TIMESTAMP = 'X-BAPI-TIMESTAMP';
    private const HEADER_SIGN = 'X-BAPI-SIGN';
    private const HEADER_RECV_WINDOW = 'X-BAPI-RECV-WINDOW';
    private const HEADER_REQUEST_ID = 'cdn-request-id';

    public function __construct(
        private readonly ByBitConfig $config,
        private readonly RebitHttpClientInterface $httpClient,
        private readonly RequestIdGeneratorInterface $requestIdGenerator,
    ) {
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function get(string $endpoint, array $params = []): array
    {
        $queryString = http_build_query($params);
        $url = $this->buildUrl($endpoint, $queryString);
        $headers = $this->buildHeaders($queryString);

        $response = $this->httpClient->get($url, $headers);

        return $response->toArray();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function post(string $endpoint, array $data = []): array
    {
        $jsonBody = json_encode($data, JSON_THROW_ON_ERROR);
        $url = $this->buildUrl($endpoint);
        $headers = $this->buildHeaders($jsonBody);
        $headers['Content-Type'] = 'application/json';

        $response = $this->httpClient->post($url, $headers, $jsonBody);

        return $response->toArray();
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
            self::HEADER_RECV_WINDOW => (string) $this->config->getRecvWindow(),
            self::HEADER_REQUEST_ID => $this->requestIdGenerator->generate(),
        ];
    }

    private function generateSignature(string $timestamp, string $payload): string
    {
        $signString = $timestamp
            . $this->config->getApiKey()
            . $this->config->getRecvWindow()
            . $payload;

        return strtolower(
            hash_hmac('sha256', $signString, $this->config->getApiSecret())
        );
    }

    private function getTimestamp(): string
    {
        return (string) (int) (microtime(true) * 1000);
    }
}