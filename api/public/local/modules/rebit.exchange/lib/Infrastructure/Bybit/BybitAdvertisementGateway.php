<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Адаптер для управления объявлениями через Bybit P2P API.
 */
final readonly class BybitAdvertisementGateway implements BybitAdvertisementGatewayInterface
{
    private const string CREATE_ENDPOINT = '/v5/p2p/item/create';
    private const string UPDATE_ENDPOINT = '/v5/p2p/item/update';
    private const string CANCEL_ENDPOINT = '/v5/p2p/item/cancel';
    private const string PERSONAL_LIST_ENDPOINT = '/v5/p2p/item/personal/list';
    private const string INFO_ENDPOINT = '/v5/p2p/item/info';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    public function create(int $userId, array $params): string
    {
        $response = $this->post($userId, self::CREATE_ENDPOINT, $params);

        return (string)($response['itemId'] ?? '');
    }

    public function update(int $userId, array $params): void
    {
        $this->post($userId, self::UPDATE_ENDPOINT, $params);
    }

    public function cancel(int $userId, string $bybitAdId): void
    {
        $this->post($userId, self::CANCEL_ENDPOINT, ['itemId' => $bybitAdId]);
    }

    public function fetchPersonalList(int $userId, array $params = []): array
    {
        return $this->post($userId, self::PERSONAL_LIST_ENDPOINT, $params);
    }

    public function fetchInfo(int $userId, string $bybitAdId): array
    {
        return $this->post($userId, self::INFO_ENDPOINT, ['itemId' => $bybitAdId]);
    }

    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    private function post(int $userId, string $endpoint, array $body): array
    {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->post(
                $endpoint,
                $connection->credentials,
                $connection->environment,
                $body,
            );
        } catch (BybitApiException $e) {
            throw new HttpException(
                "Bybit API error [{$endpoint}]: " . $e->getMessage(),
                502,
            );
        }

        return $response->result;
    }
}
