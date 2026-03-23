<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

use Rebit\Share\Shared\Exception\HttpException;

/**
 * Контракт для резолва подключения к Bybit по userId.
 * Реализация в модуле rebit.identity.
 */
interface BybitConnectionResolverInterface
{
    /**
     * @throws HttpException если активное подключение не найдено
     */
    public function resolve(int $userId): BybitConnectionDto;

    /**
     * @return array<int, int> список userId с активным подключением
     */
    public function getActiveUserIds(): array;
}
