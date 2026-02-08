<?php

declare(strict_types=1);

namespace Rebit\Bybit\Controller;

use Rebit\Bybit\Application\Advertisement\Dto\Request\AdInfoRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Request\CancelAdRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Request\CreateAdRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Request\OrderBookRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Request\PersonalListRequestDto;
use Rebit\Bybit\Application\Advertisement\Dto\Request\UpdateAdRequestDto;
use Rebit\Bybit\Application\Advertisement\UseCase\AdInfoUseCase;
use Rebit\Bybit\Application\Advertisement\UseCase\CancelAdUseCase;
use Rebit\Bybit\Application\Advertisement\UseCase\CreateAdUseCase;
use Rebit\Bybit\Application\Advertisement\UseCase\OrderBookUseCase;
use Rebit\Bybit\Application\Advertisement\UseCase\PersonalListUseCase;
use Rebit\Bybit\Application\Advertisement\UseCase\UpdateAdUseCase;
use Rebit\Bybit\Infrastructure\Http\Exception\ByBitHttpException;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;
use Rebit\Share\Infrastructure\HttpClient\Exception\RebitHttpClientException;

final class ApiAdsController extends BaseJsonController
{
    public function __construct(
        private readonly OrderBookUseCase $useCase,
        private readonly CreateAdUseCase $createAdUseCase,
        private readonly CancelAdUseCase $cancelAdUseCase,
        private readonly UpdateAdUseCase $updateAdUseCase,
        private readonly PersonalListUseCase $personalListUseCase,
        private readonly AdInfoUseCase $adInfoUseCase,
    ) {
        parent::__construct();
    }

    /**
     * @throws RebitHttpClientException
     * @throws ByBitHttpException
     * @throws \JsonException
     */
    public function GetAdsAction(OrderBookRequestDto $dto): ControllerJson
    {
        return $this->json($this->useCase->execute($dto));
    }

    /**
     * @throws RebitHttpClientException
     * @throws ByBitHttpException
     * @throws \JsonException
     */
    public function CreateAdsAction(CreateAdRequestDto $dto): ControllerJson
    {
        return $this->json($this->createAdUseCase->execute($dto));
    }

    /**
     * @throws RebitHttpClientException
     * @throws ByBitHttpException
     * @throws \JsonException
     */
    public function CancelAdAction(CancelAdRequestDto $dto): ControllerJson
    {
        $this->cancelAdUseCase->execute($dto);

        return $this->json(['success' => true]);
    }

    /**
     * @throws RebitHttpClientException
     * @throws ByBitHttpException
     * @throws \JsonException
     */
    public function UpdateAdAction(UpdateAdRequestDto $dto): ControllerJson
    {
        return $this->json($this->updateAdUseCase->execute($dto));
    }

    /**
     * @throws RebitHttpClientException
     * @throws ByBitHttpException
     * @throws \JsonException
     */
    public function PersonalListAction(PersonalListRequestDto $dto): ControllerJson
    {
        return $this->json($this->personalListUseCase->execute($dto));
    }

    /**
     * @throws RebitHttpClientException
     * @throws ByBitHttpException
     * @throws \JsonException
     */
    public function AdInfoAction(AdInfoRequestDto $dto): ControllerJson
    {
        return $this->json($this->adInfoUseCase->execute($dto));
    }
}
