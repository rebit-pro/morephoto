<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\TradeChat\Dto\Bybit\BybitTradeChatMessageDto;
use Rebit\Exchange\Application\TradeChat\Dto\Bybit\BybitTradeChatMessageListDto;
use Rebit\Exchange\Application\TradeChat\Dto\Bybit\BybitTradeChatUploadResultDto;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Адаптер для отправки сообщений в чат через Bybit P2P API.
 * POST /v5/p2p/order/message/send
 */
final readonly class BybitChatGateway implements BybitChatGatewayInterface
{
    private const string SEND_ENDPOINT = '/v5/p2p/order/message/send';
    private const string UPLOAD_ENDPOINT = '/v5/p2p/oss/upload_file';
    private const string QUERY_LIST_ENDPOINT = '/v5/p2p/order/message/queryList';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    public function sendMessage(
        int $userId,
        string $orderId,
        string $message,
        string $contentType,
        string $msgUuid,
        ?string $fileName = null,
    ): void {
        $connection = $this->connectionResolver->resolve($userId);

        $body = [
            'orderId' => $orderId,
            'message' => $message,
            'contentType' => $contentType,
            'msgUuid' => $msgUuid,
        ];

        if (null !== $fileName && '' !== $fileName) {
            $body['fileName'] = $fileName;
        }

        try {
            $this->bybitClient->post(
                self::SEND_ENDPOINT,
                $connection->credentials,
                $connection->environment,
                $body,
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                'Ошибка отправки сообщения в Bybit: ' . $e->getMessage(),
                502,
            );
        }
    }

    public function uploadFile(
        int $userId,
        string $filePath,
        string $fileName,
        string $mimeType,
    ): BybitTradeChatUploadResultDto {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->postMultipart(
                self::UPLOAD_ENDPOINT,
                $connection->credentials,
                $connection->environment,
                [],
                [
                    'upload_file' => [
                        'path' => $filePath,
                        'name' => $fileName,
                        'mimeType' => $mimeType,
                    ],
                ],
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                'Ошибка загрузки файла в Bybit: ' . $e->getMessage(),
                502,
            );
        }

        $url = (string)($response->result['url'] ?? '');

        return new BybitTradeChatUploadResultDto(
            url: $this->normalizeUploadedFileUrl($url, $connection->environment),
            type: (string)($response->result['type'] ?? ''),
        );
    }

    public function fetchMessages(
        int $userId,
        string $orderId,
        int $page = 1,
        int $size = 50,
    ): BybitTradeChatMessageListDto {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->post(
                self::QUERY_LIST_ENDPOINT,
                $connection->credentials,
                $connection->environment,
                [
                    'orderId' => $orderId,
                    'page' => (string)$page,
                    'size' => (string)$size,
                ],
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                'Ошибка получения сообщений из Bybit: ' . $e->getMessage(),
                502,
            );
        }

        /** @var array<int, array<string, mixed>> $messages */
        $messages = is_array($response->result['messages'] ?? null)
            ? $response->result['messages']
            : [];

        return new BybitTradeChatMessageListDto(
            messages: array_map(
                static fn(array $message): BybitTradeChatMessageDto => new BybitTradeChatMessageDto(
                    id: (string)($message['id'] ?? ''),
                    message: (string)($message['message'] ?? ''),
                    contentType: (string)($message['contentType'] ?? ''),
                    fileName: (string)($message['fileName'] ?? ''),
                    userId: (string)($message['userId'] ?? ''),
                    nickName: (string)($message['nickName'] ?? ''),
                    createDate: (string)($message['createDate'] ?? ''),
                ),
                $messages,
            ),
        );
    }

    private function normalizeUploadedFileUrl(string $url, BybitEnvironmentEnum $environment): string
    {
        if ('' === $url || str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        $baseUrl = $environment->baseUrl();
        $scheme = (string)parse_url($baseUrl, PHP_URL_SCHEME);
        $host = (string)parse_url($baseUrl, PHP_URL_HOST);

        if ('' === $scheme || '' === $host) {
            return $url;
        }

        $port = parse_url($baseUrl, PHP_URL_PORT);
        $origin = $scheme . '://' . $host;

        if (null !== $port) {
            $origin .= ':' . $port;
        }

        return $origin . $url;
    }
}
