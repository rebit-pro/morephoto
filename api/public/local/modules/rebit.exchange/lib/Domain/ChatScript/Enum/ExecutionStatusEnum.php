<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\ChatScript\Enum;

enum ExecutionStatusEnum: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
