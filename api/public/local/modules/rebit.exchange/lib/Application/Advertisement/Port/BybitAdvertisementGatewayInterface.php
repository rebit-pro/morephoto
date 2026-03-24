<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Port;

use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для управления объявлениями через Bybit API.
 */
interface BybitAdvertisementGatewayInterface
{
    /**
     * Создать объявление. POST /v5/p2p/item/create
     *
     * @param array<string, mixed> $params
     *
     * @return string ID созданного объявления (itemId)
     *
     * @throws HttpException
     */
    public function create(int $userId, array $params): string;

    /**
     * Обновить объявление. POST /v5/p2p/item/update
     *
     * @param array<string, mixed> $params
     *
     * @throws HttpException
     */
    public function update(int $userId, array $params): void;

    /**
     * Удалить (отменить) объявление. POST /v5/p2p/item/cancel
     *
     * @throws HttpException
     */
    public function cancel(int $userId, string $bybitAdId): void;

    /**
     * Получить список объявлений пользователя. POST /v5/p2p/item/personal/list
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    public function fetchPersonalList(int $userId, array $params = []): array;

    /**
     * Получить детали объявления. POST /v5/p2p/item/info
     *
     * @return array<string, mixed>
     *
     * @throws HttpException
     */
    public function fetchInfo(int $userId, string $bybitAdId): array;
}
