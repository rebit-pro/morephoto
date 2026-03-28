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

        /** @var array<string, mixed> $profile */
        $profile = $response->result;

        /** @var array{
         *     userId: string,
         *     nickName: string,
         *     defaultNickName?: bool,
         *     isOnline?: bool,
         *     kycLevel?: int|string,
         *     email?: string,
         *     mobile?: string,
         *     lastLogoutTime?: string,
         *     recentRate?: int|string,
         *     totalFinishCount?: int|string,
         *     totalFinishSellCount?: int|string,
         *     totalFinishBuyCount?: int|string,
         *     recentFinishCount?: int|string,
         *     averageReleaseTime?: string,
         *     averageTransferTime?: string,
         *     accountCreateDays?: int|string,
         *     firstTradeDays?: int|string,
         *     realName?: string,
         *     recentTradeAmount?: string,
         *     totalTradeAmount?: string,
         *     registerTime?: string,
         *     authStatus?: int|string,
         *     kycCountryCode?: string,
         *     blocked?: string,
         *     goodAppraiseRate?: string,
         *     goodAppraiseCount?: int|string,
         *     badAppraiseCount?: int|string,
         *     vipLevel?: int|string,
         *     realNameEn?: string,
         *     userType?: string,
         * } $normalizedProfile
         */
        $normalizedProfile = [
            'userId' => (string)($profile['userId'] ?? ''),
            'nickName' => (string)($profile['nickName'] ?? ''),
        ];

        foreach ([
            'defaultNickName',
            'isOnline',
            'kycLevel',
            'email',
            'mobile',
            'lastLogoutTime',
            'recentRate',
            'totalFinishCount',
            'totalFinishSellCount',
            'totalFinishBuyCount',
            'recentFinishCount',
            'averageReleaseTime',
            'averageTransferTime',
            'accountCreateDays',
            'firstTradeDays',
            'realName',
            'recentTradeAmount',
            'totalTradeAmount',
            'registerTime',
            'authStatus',
            'kycCountryCode',
            'blocked',
            'goodAppraiseRate',
            'goodAppraiseCount',
            'badAppraiseCount',
            'vipLevel',
            'realNameEn',
            'userType',
        ] as $field) {
            if (array_key_exists($field, $profile)) {
                $normalizedProfile[$field] = $profile[$field];
            }
        }

        return $normalizedProfile;
    }
}
