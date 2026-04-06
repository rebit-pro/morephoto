<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Currency\Port\BybitSpotTickerGatewayInterface;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrenciesUseCase;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrencyPairsUseCase;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Infrastructure\Adapter\CurrencyRubRateQueryAdapter;
use Rebit\Exchange\Infrastructure\Adapter\CurrencyQueryAdapter;
use Rebit\Exchange\Infrastructure\Bybit\BybitSpotTickerGateway;
use Rebit\Exchange\Presentation\Controller\CurrencyController;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;
use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClientFactory;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

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
    BybitSpotTickerGatewayInterface::class => [
        'constructor' => static function(): BybitSpotTickerGatewayInterface {
            $logger = Log::getLogger(LogChannelEnum::exchange);

            return new BybitSpotTickerGateway(
                RebitHttpClientFactory::create($logger),
                BybitEnvironmentEnum::Mainnet->baseUrl(),
                $logger,
            );
        },
    ],
    CurrencyRubRateQueryInterface::class => [
        'constructor' => static function(): CurrencyRubRateQueryInterface {
            $sl = ServiceLocator::getInstance();

            return new CurrencyRubRateQueryAdapter(
                $sl->get(CurrencyPairRepository::class),
                $sl->get(OrderBookRepository::class),
                $sl->get(BybitSpotTickerGatewayInterface::class),
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
