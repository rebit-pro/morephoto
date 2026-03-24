<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Enum;

enum CancelReasonEnum: string
{
    case Timeout = 'timeout';
    case User = 'user';
    case InsufficientFunds = 'insufficient_funds';
    case Dispute = 'dispute';
}
