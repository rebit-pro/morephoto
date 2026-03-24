<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Advertisement\Enum;

enum AdvertisementStatusEnum: string
{
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Маппинг из Bybit API item status.
     */
    public static function fromBybit(int $status): self
    {
        return match ($status) {
            10 => self::Active,
            20 => self::Paused,
            30 => self::Completed,
            default => throw new \InvalidArgumentException("Unknown Bybit advertisement status: {$status}"),
        };
    }
}
