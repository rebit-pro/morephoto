<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Dto\Bybit;

final readonly class BybitCounterpartyProfileDto
{
    public function __construct(
        public string $userId,
        public string $nickName,
        public string $realName,
        public string $realNameEn,
        public int $kycLevel,
        public string $kycCountryCode,
        public bool $isOnline,
        public int $totalFinishCount,
        public int $totalFinishBuyCount,
        public int $totalFinishSellCount,
        public string $recentRate,
        public int $recentFinishCount,
        public string $averageReleaseTime,
        public string $averageTransferTime,
        public int $accountCreateDays,
        public int $firstTradeDays,
        public string $totalTradeAmount,
        public string $recentTradeAmount,
        public string $goodAppraiseRate,
        public int $goodAppraiseCount,
        public int $badAppraiseCount,
        public int $authStatus,
        public int $vipLevel,
        public string $userType,
        public string $blocked,
        public string $registerTime,
    ) {}
}
