<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Port;

use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitCounterpartyProfileDto;
use Rebit\Share\Shared\Exception\HttpException;

interface BybitCounterpartyGatewayInterface
{
    /**
     * @throws HttpException
     */
    public function fetchProfile(int $userId, string $originalUid, string $orderId): BybitCounterpartyProfileDto;
}
