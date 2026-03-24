<?php

declare(strict_types=1);
use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrenciesUseCase;
use Rebit\Exchange\Application\Currency\UseCase\GetCurrencyPairsUseCase;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;
use Rebit\Exchange\Infrastructure\Adapter\CurrencyQueryAdapter;
use Rebit\Exchange\Presentation\Controller\CurrencyController;
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
    GetCurrenciesUseCase::class => [
        'constructor' => static function(): GetCurrenciesUseCase {
            return new GetCurrenciesUseCase(
                ServiceLocator::getInstance()->get(CurrencyRepository::class),
            );
        },
    ],
    GetCurrencyPairsUseCase::class => [
        'constructor' => static function(): GetCurrencyPairsUseCase {
            $sl = ServiceLocator::getInstance();

            return new GetCurrencyPairsUseCase(
                $sl->get(CurrencyPairRepository::class),
                $sl->get(CurrencyRepository::class),
            );
        },
    ],
    CurrencyController::class => [
        'constructor' => static function(): CurrencyController {
            $sl = ServiceLocator::getInstance();

            return new CurrencyController(
                $sl->get(GetCurrenciesUseCase::class),
                $sl->get(GetCurrencyPairsUseCase::class),
            );
        },
    ],
];
