<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Shared\Enum;

/**
 * Направление сделки/ордера (buy/sell).
 * Используется в OrderBook, Advertisement, Trade.
 */
enum SideEnum: string
{
    case Buy = 'buy';
    case Sell = 'sell';

    /**
     * Маппинг из Bybit API (0 = buy, 1 = sell).
     */
    public static function fromBybit(int|string $side): self
    {
        return match ((string)$side) {
            '0' => self::Buy,
            '1' => self::Sell,
            default => throw new \InvalidArgumentException("Unknown Bybit side: {$side}"),
        };
    }

    /**
     * Преобразование в формат Bybit API.
     */
    public function toBybit(): string
    {
        return match ($this) {
            self::Buy => '0',
            self::Sell => '1',
        };
    }
}
