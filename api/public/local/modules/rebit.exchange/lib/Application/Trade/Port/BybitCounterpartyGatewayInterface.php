<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Port;

use Rebit\Share\Shared\Exception\HttpException;

interface BybitCounterpartyGatewayInterface
{
    /**
     * @return array{
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
     * }
     *
     * @throws HttpException
     */
    public function fetchProfile(int $userId, string $originalUid, string $orderId): array;
}
