<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Enum;

enum ConnectionModeEnum: string
{
    case Testnet = 'testnet';
    case Mainnet = 'mainnet';
}
