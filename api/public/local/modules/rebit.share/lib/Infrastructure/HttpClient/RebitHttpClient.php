<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\HttpClient;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;
use Psr\Log\LoggerInterface;

final readonly class RebitHttpClient
{
    private const int DEFAULT_SOCKET_TIMEOUT = 30;
    private const int DEFAULT_STREAM_TIMEOUT = 60;

    public function __construct(
        private LoggerInterface $logger,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     *
     * @return array<string, mixed>
     *
     * @throws RebitHttpClientException
     */
    public function post(string $url, array $data, array $headers = []): array
    {
        $httpClient = $this->createHttpClient($headers);

        $this->logRequest('POST', $url, $data);

        $response = $httpClient->post($url, $data);

        if (false === $response) {
            $this->logError($url, $httpClient->getError());

            throw new RebitHttpClientException(
                sprintf('HTTP POST request failed: %s', implode('; ', $httpClient->getError())),
            );
        }

        $this->logResponse($url, $response, $httpClient->getStatus(), $httpClient->getHeaders());

        return $this->parseResponse($response);
    }

    /**
     * @param array<string, string> $headers
     *
     * @return array<string, mixed>
     *
     * @throws RebitHttpClientException
     */
    public function get(string $url, array $headers = []): array
    {
        $httpClient = $this->createHttpClient($headers);

        $this->logRequest('GET', $url);

        $response = $httpClient->get($url);

        if (false === $response) {
            $this->logError($url, $httpClient->getError());

            throw new RebitHttpClientException(
                sprintf('HTTP GET request failed: %s', implode('; ', $httpClient->getError())),
            );
        }

        $this->logResponse($url, $response, $httpClient->getStatus());

        return $this->parseResponse($response);
    }

    /**
     * @param array<string, string> $headers
     */
    private function createHttpClient(array $headers): HttpClient
    {
        $httpClient = new HttpClient([
            'socketTimeout' => self::DEFAULT_SOCKET_TIMEOUT,
            'streamTimeout' => self::DEFAULT_STREAM_TIMEOUT,
        ]);

        foreach ($headers as $name => $value) {
            $httpClient->setHeader($name, $value);
        }

        return $httpClient;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function logRequest(string $method, string $url, array $data = []): void
    {
        $this->logger->info('HTTP request', [
            'method' => $method,
            'url' => $url,
            'data' => $data,
        ]);
    }

    /**
     * @param array<string, string> $errors
     */
    private function logError(string $url, array $errors): void
    {
        $this->logger->error('HTTP request failed', [
            'url' => $url,
            'errors' => $errors,
        ]);
    }

    private function logResponse(string $url, string $response, int $status, $headers = []): void
    {
        $this->logger->info('HTTP response', [
            'url' => $url,
            'headers' => $headers,
            'status' => $status,
            'response_length' => strlen($response),
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RebitHttpClientException
     */
    private function parseResponse(string $response): array
    {
        try {
            return Json::decode($response);
        } catch (\Exception $e) {
            throw new RebitHttpClientException(
                sprintf('Failed to parse JSON response: %s', $e->getMessage()),
            );
        }
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function maskSensitiveData(array $data): array
    {
        $sensitiveKeys = ['password', 'token', 'secret', 'api_key', 'authorization'];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '\*\*\*';
            }
        }

        return $data;
    }
}