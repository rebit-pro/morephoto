<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Port;

use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Wallet\Application\Balance\Dto\Bybit\BybitBalanceListDto;

/**
 * Порт для получения балансов из Bybit API.
 * Реализация в Infrastructure скрывает endpoint, параметры и парсинг ответа.
 */
interface BybitBalanceGatewayInterface
{
    /**
     * Получить список монет с балансами для указанного пользователя.
     *
     * @throws HttpException
     */
    public function fetchBalances(int $userId): BybitBalanceListDto;
}
