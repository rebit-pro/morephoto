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
}
