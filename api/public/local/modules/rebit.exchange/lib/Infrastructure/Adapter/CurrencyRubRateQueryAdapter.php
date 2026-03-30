<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Adapter;

use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Domain\Shared\Enum\SideEnum;
use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Адаптер приблизительного курса валюты к RUB.
 *
 * Источник курса — локально сохранённый P2P-стакан Bybit.
 * Для оценки используется лучшая цена покупки (максимальный bid),
 * по аналогии с тем, как ранее это считал фронт.
 */
final readonly class CurrencyRubRateQueryAdapter implements CurrencyRubRateQueryInterface
{
    public function __construct(
        private CurrencyPairRepository $currencyPairRepository,
        private OrderBookRepository $orderBookRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function findApproximateRubRateByCurrencyCode(string $currencyCode): ?float
    {
        $normalizedCurrencyCode = mb_strtoupper($currencyCode);

        if ('RUB' === $normalizedCurrencyCode) {
            return 1.0;
        }

        $currencyPair = $this->currencyPairRepository->findByTokenAndFiat($normalizedCurrencyCode, 'RUB');
        if (null === $currencyPair) {
            return null;
        }

        $entries = $this->orderBookRepository->findByCurrencyPairAndSide(
            $currencyPair->getId(),
            SideEnum::Buy->value,
        );

        $bestPrice = null;

        foreach ($entries as $entry) {
            $price = $entry->getUfPrice();

            if (0.0 >= $price) {
                continue;
            }

            if (null === $bestPrice || $price > $bestPrice) {
                $bestPrice = $price;
            }
        }

        return $bestPrice;
    }
}
