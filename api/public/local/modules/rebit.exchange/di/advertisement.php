<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Exchange\Application\Advertisement\UseCase\CreateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\DeactivateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\ListAdvertisementsUseCase;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Infrastructure\Bybit\BybitAdvertisementGateway;
use Rebit\Exchange\Presentation\Controller\AdvertisementController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Wallet\BalanceQueryInterface;

return [
    AdvertisementRepository::class => [
        'className' => AdvertisementRepository::class,
    ],
    BybitAdvertisementGatewayInterface::class => [
        'constructor' => static function(): BybitAdvertisementGatewayInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitAdvertisementGateway(
                $sl->get(BybitConnectionResolverInterface::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    CreateAdvertisementUseCase::class => [
        'constructor' => static function(): CreateAdvertisementUseCase {
            $sl = ServiceLocator::getInstance();

            return new CreateAdvertisementUseCase(
                $sl->get(AdvertisementRepository::class),
                $sl->get(CurrencyPairRepository::class),
                $sl->get(BybitAdvertisementGatewayInterface::class),
                $sl->get(BalanceQueryInterface::class),
            );
        },
    ],
    ListAdvertisementsUseCase::class => [
        'constructor' => static function(): ListAdvertisementsUseCase {
            return new ListAdvertisementsUseCase(
                ServiceLocator::getInstance()->get(AdvertisementRepository::class),
            );
        },
    ],
    DeactivateAdvertisementUseCase::class => [
        'constructor' => static function(): DeactivateAdvertisementUseCase {
            $sl = ServiceLocator::getInstance();

            return new DeactivateAdvertisementUseCase(
                $sl->get(AdvertisementRepository::class),
                $sl->get(BybitAdvertisementGatewayInterface::class),
            );
        },
    ],
    AdvertisementController::class => [
        'constructor' => static function(): AdvertisementController {
            $sl = ServiceLocator::getInstance();

            return new AdvertisementController(
                $sl->get(CreateAdvertisementUseCase::class),
                $sl->get(ListAdvertisementsUseCase::class),
                $sl->get(DeactivateAdvertisementUseCase::class),
            );
        },
    ],
];
