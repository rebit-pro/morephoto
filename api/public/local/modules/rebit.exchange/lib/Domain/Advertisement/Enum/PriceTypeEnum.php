<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Advertisement\Enum;

enum PriceTypeEnum: string
{
    case Fixed = 'fixed';
    case Floating = 'floating';

    /**
     * Маппинг из Bybit API priceType.
     */
    public static function fromBybit(int|string $priceType): self
    {
        return match ((string)$priceType) {
            '0' => self::Fixed,
            '1' => self::Floating,
            default => throw new \InvalidArgumentException("Unknown Bybit priceType: {$priceType}"),
        };
    }

    public function toBybit(): string
    {
        return match ($this) {
            self::Fixed => '0',
            self::Floating => '1',
        };
    }
}
