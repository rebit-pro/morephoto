<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\Trade\Port\BybitCounterpartyGatewayInterface;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class BybitCounterpartyGateway implements BybitCounterpartyGatewayInterface
{
    private const string PERSONAL_INFO_ENDPOINT = '/v5/p2p/user/order/personal/info';

    public function __construct(
        private BybitConnectionResolverInterface $connectionResolver,
        private BybitClientInterface $bybitClient,
    ) {}

    public function fetchProfile(int $userId, string $originalUid, string $orderId): array
    {
        $connection = $this->connectionResolver->resolve($userId);

        try {
            $response = $this->bybitClient->post(
                self::PERSONAL_INFO_ENDPOINT,
                $connection->credentials,
                $connection->environment,
                [
                    'originalUid' => $originalUid,
                    'orderId' => $orderId,
                ],
            );
        } catch (BybitApiException $exception) {
            throw new HttpException(
                'Bybit API error [' . self::PERSONAL_INFO_ENDPOINT . ']: ' . $exception->getMessage(),
                502,
            );
        }

        return $response->result;
    }
}
