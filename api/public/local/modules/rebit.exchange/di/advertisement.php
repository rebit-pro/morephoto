<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Exchange\Application\Advertisement\UseCase\CreateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\DeactivateAdvertisementUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\ListAdvertisementsUseCase;
use Rebit\Exchange\Application\Advertisement\UseCase\ToggleAdvertisementUseCase;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
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
        'className' => CreateAdvertisementUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(AdvertisementRepository::class),
            ServiceLocator::getInstance()->get(CurrencyPairRepository::class),
            ServiceLocator::getInstance()->get(OrderBookRepository::class),
            ServiceLocator::getInstance()->get(BybitAdvertisementGatewayInterface::class),
            ServiceLocator::getInstance()->get(BalanceQueryInterface::class),
        ],
    ],
    ListAdvertisementsUseCase::class => [
        'className' => ListAdvertisementsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(AdvertisementRepository::class),
        ],
    ],
    DeactivateAdvertisementUseCase::class => [
        'className' => DeactivateAdvertisementUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(AdvertisementRepository::class),
            ServiceLocator::getInstance()->get(BybitAdvertisementGatewayInterface::class),
        ],
    ],
    ToggleAdvertisementUseCase::class => [
        'className' => ToggleAdvertisementUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(AdvertisementRepository::class),
            ServiceLocator::getInstance()->get(CurrencyPairRepository::class),
            ServiceLocator::getInstance()->get(BybitAdvertisementGatewayInterface::class),
        ],
    ],
    AdvertisementController::class => [
        'className' => AdvertisementController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(CreateAdvertisementUseCase::class),
            ServiceLocator::getInstance()->get(ListAdvertisementsUseCase::class),
            ServiceLocator::getInstance()->get(DeactivateAdvertisementUseCase::class),
            ServiceLocator::getInstance()->get(ToggleAdvertisementUseCase::class),
        ],
    ],
];
