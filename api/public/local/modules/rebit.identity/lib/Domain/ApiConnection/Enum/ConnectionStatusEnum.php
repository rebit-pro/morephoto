<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Enum;

enum ConnectionStatusEnum: string
{
    case Active = 'active';
    case Invalid = 'invalid';
    case Revoked = 'revoked';
    case PendingVerification = 'pending_verification';
}
