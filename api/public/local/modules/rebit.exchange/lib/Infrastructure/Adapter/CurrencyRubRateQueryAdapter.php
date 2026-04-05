<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Adapter;

use Rebit\Exchange\Application\Currency\Port\BybitSpotTickerGatewayInterface;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\OrderBook\Entity\OrderBookEntry;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Exchange\Domain\Shared\Enum\SideEnum;
use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Адаптер приблизительного курса валюты к RUB.
 *
 * Стратегия:
 * 1. Прямой P2P-курс из локального стакана (TOKEN_RUB).
 * 2. Кросс-курс через USDT: TOKEN→USDT (spot) × USDT→RUB (P2P).
 */
final readonly class CurrencyRubRateQueryAdapter implements CurrencyRubRateQueryInterface
{
    public function __construct(
        private CurrencyPairRepository $currencyPairRepository,
        private OrderBookRepository $orderBookRepository,
        private BybitSpotTickerGatewayInterface $spotTickerGateway,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function findApproximateRubRateByCurrencyCode(string $currencyCode): ?float
    {
        $code = mb_strtoupper($currencyCode);

        if ('RUB' === $code) {
            return 1.0;
        }

        // 1. Прямой P2P-курс (например, USDT_RUB, BTC_RUB)
        $directRate = $this->findBestBuyPrice($code, 'RUB');
        if (null !== $directRate) {
            return $directRate;
        }

        // 2. Кросс-курс через USDT: TOKEN→USDT (spot) × USDT→RUB (P2P)
        if ('USDT' !== $code) {
            $usdtRubRate = $this->findBestBuyPrice('USDT', 'RUB');

            if (null !== $usdtRubRate) {
                $spotPrice = $this->spotTickerGateway->getLastPrice($code . 'USDT');

                if (null !== $spotPrice) {
                    return $spotPrice * $usdtRubRate;
                }
            }
        }

        return null;
    }

    /**
     * Лучшая цена покупки (bid) из P2P-стакана для пары token/fiat.
     *
     * @throws RepositoryException
     */
    private function findBestBuyPrice(string $token, string $fiat): ?float
    {
        $currencyPair = $this->currencyPairRepository->findByTokenAndFiat($token, $fiat);
        if (null === $currencyPair) {
            return null;
        }

        $entries = $this->orderBookRepository->findByCurrencyPairAndSide(
            $currencyPair->getId(),
            SideEnum::Buy->value,
        );

        $bestPrice = null;

        /** @var OrderBookEntry $entry */
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
