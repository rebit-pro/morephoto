<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Transaction\Enum;

enum TransactionTypeEnum: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case TradeBuy = 'trade_buy';
    case TradeSell = 'trade_sell';
    case Lock = 'lock';
    case Unlock = 'unlock';
    case Fee = 'fee';

    /**
     * Является ли тип транзакции приходной операцией.
     */
    public function isIncoming(): bool
    {
        return match ($this) {
            self::Deposit, self::TradeSell, self::Unlock => true,
            default => false,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<int, string>
     */
    public static function incomingValues(): array
    {
        return array_map(
            static fn(self $type): string => $type->value,
            array_filter(self::cases(), static fn(self $type): bool => $type->isIncoming()),
        );
    }

    /**
     * @return array<int, string>
     */
    public static function outgoingValues(): array
    {
        return array_map(
            static fn(self $type): string => $type->value,
            array_filter(self::cases(), static fn(self $type): bool => !$type->isIncoming()),
        );
    }
}
