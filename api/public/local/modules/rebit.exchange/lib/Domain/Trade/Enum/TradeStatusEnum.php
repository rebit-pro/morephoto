<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Enum;

enum TradeStatusEnum: string
{
    case PendingPayment = 'pending_payment';
    case PaymentSent = 'payment_sent';
    case PaymentConfirmed = 'payment_confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Disputed = 'disputed';

    /**
     * Маппинг из Bybit API order status.
     *
     * @see docs/api.md — Приложение А
     */
    public static function fromBybit(int $status): self
    {
        return match ($status) {
            10, 60, 70 => self::PendingPayment,
            20 => self::PaymentSent,
            30 => self::Disputed,
            40 => self::Cancelled,
            50 => self::Completed,
            default => throw new \InvalidArgumentException("Unknown Bybit order status: {$status}"),
        };
    }

    /**
     * Допускает ли статус активный чат.
     */
    public function isChatActive(): bool
    {
        return match ($this) {
            self::PendingPayment,
            self::PaymentSent,
            self::PaymentConfirmed,
            self::Disputed => true,
            self::Completed,
            self::Cancelled => false,
        };
    }
}
