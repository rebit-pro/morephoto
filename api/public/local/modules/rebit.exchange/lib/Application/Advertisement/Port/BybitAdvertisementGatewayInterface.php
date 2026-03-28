<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Port;

use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitAdvertisementCreateResultDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitAdvertisementInfoDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitAdvertisementListDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitCreateAdvertisementDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitPersonalAdvertisementListRequestDto;
use Rebit\Exchange\Application\Advertisement\Dto\Bybit\BybitUpdateAdvertisementDto;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Порт для управления объявлениями через Bybit API.
 */
interface BybitAdvertisementGatewayInterface
{
    /**
     * Создать объявление. POST /v5/p2p/item/create
     *
     * @throws HttpException
     */
    public function create(int $userId, BybitCreateAdvertisementDto $dto): BybitAdvertisementCreateResultDto;

    /**
     * Обновить объявление. POST /v5/p2p/item/update
     *
     * @throws HttpException
     */
    public function update(int $userId, BybitUpdateAdvertisementDto $dto): void;

    /**
     * Удалить (отменить) объявление. POST /v5/p2p/item/cancel
     *
     * @throws HttpException
     */
    public function cancel(int $userId, string $bybitAdId): void;

    /**
     * Получить список объявлений пользователя. POST /v5/p2p/item/personal/list
     *
     * @throws HttpException
     */
    public function fetchPersonalList(
        int $userId,
        BybitPersonalAdvertisementListRequestDto $dto = new BybitPersonalAdvertisementListRequestDto(),
    ): BybitAdvertisementListDto;

    /**
     * Получить детали объявления. POST /v5/p2p/item/info
     *
     * @throws HttpException
     */
    public function fetchInfo(int $userId, string $bybitAdId): BybitAdvertisementInfoDto;
}
