<?php

declare(strict_types=1);
use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrenciesUseCase;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrencyPairsUseCase;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Infrastructure\Adapter\CurrencyRubRateQueryAdapter;
use Rebit\Exchange\Infrastructure\Adapter\CurrencyQueryAdapter;
use Rebit\Exchange\Presentation\Controller\CurrencyController;
use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;

return [
    CurrencyRepository::class => [
        'className' => CurrencyRepository::class,
    ],
    CurrencyPairRepository::class => [
        'className' => CurrencyPairRepository::class,
    ],
    CurrencyQueryInterface::class => [
        'constructor' => static function(): CurrencyQueryInterface {
            return new CurrencyQueryAdapter(
                ServiceLocator::getInstance()->get(CurrencyRepository::class),
            );
        },
    ],
    CurrencyRubRateQueryInterface::class => [
        'constructor' => static function(): CurrencyRubRateQueryInterface {
            return new CurrencyRubRateQueryAdapter(
                ServiceLocator::getInstance()->get(CurrencyPairRepository::class),
                ServiceLocator::getInstance()->get(OrderBookRepository::class),
            );
        },
    ],
    GetCurrenciesUseCase::class => [
        'className' => GetCurrenciesUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(CurrencyRepository::class),
        ],
    ],
    GetCurrencyPairsUseCase::class => [
        'className' => GetCurrencyPairsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(CurrencyPairRepository::class),
            ServiceLocator::getInstance()->get(CurrencyRepository::class),
        ],
    ],
    CurrencyController::class => [
        'className' => CurrencyController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(GetCurrenciesUseCase::class),
            ServiceLocator::getInstance()->get(GetCurrencyPairsUseCase::class),
        ],
    ],
];
