<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Rebit\Exchange\Application\Trade\Port\BybitCounterpartyGatewayInterface;
use Rebit\Exchange\Domain\Counterparty\Dto\CounterpartyProfileDto;
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

    public function fetchProfile(int $userId, string $originalUid, string $orderId): CounterpartyProfileDto
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

        return new CounterpartyProfileDto(
            userId: (string)($profile['userId'] ?? ''),
            nickName: (string)($profile['nickName'] ?? ''),
            realName: (string)($profile['realName'] ?? ''),
            realNameEn: (string)($profile['realNameEn'] ?? ''),
            kycLevel: (int)($profile['kycLevel'] ?? 0),
            kycCountryCode: (string)($profile['kycCountryCode'] ?? ''),
            isOnline: (bool)($profile['isOnline'] ?? false),
            totalFinishCount: (int)($profile['totalFinishCount'] ?? 0),
            totalFinishBuyCount: (int)($profile['totalFinishBuyCount'] ?? 0),
            totalFinishSellCount: (int)($profile['totalFinishSellCount'] ?? 0),
            recentRate: (string)($profile['recentRate'] ?? ''),
            recentFinishCount: (int)($profile['recentFinishCount'] ?? 0),
            averageReleaseTime: (string)($profile['averageReleaseTime'] ?? ''),
            averageTransferTime: (string)($profile['averageTransferTime'] ?? ''),
            accountCreateDays: (int)($profile['accountCreateDays'] ?? 0),
            firstTradeDays: (int)($profile['firstTradeDays'] ?? 0),
            totalTradeAmount: (string)($profile['totalTradeAmount'] ?? ''),
            recentTradeAmount: (string)($profile['recentTradeAmount'] ?? ''),
            goodAppraiseRate: (string)($profile['goodAppraiseRate'] ?? ''),
            goodAppraiseCount: (int)($profile['goodAppraiseCount'] ?? 0),
            badAppraiseCount: (int)($profile['badAppraiseCount'] ?? 0),
            authStatus: (int)($profile['authStatus'] ?? 0),
            vipLevel: (int)($profile['vipLevel'] ?? 0),
            userType: (string)($profile['userType'] ?? ''),
            blocked: (string)($profile['blocked'] ?? ''),
            registerTime: (string)($profile['registerTime'] ?? ''),
        );
    }
}
