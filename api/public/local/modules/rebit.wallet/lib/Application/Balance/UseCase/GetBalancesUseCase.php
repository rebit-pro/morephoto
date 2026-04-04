<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\UseCase;

use Rebit\Share\Application\Contract\Exchange\CurrencyRubRateQueryInterface;
use Rebit\Share\Application\Contract\Exchange\CurrencyQueryInterface;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Balance\Dto\Result\BalanceListResultDto;
use Rebit\Wallet\Application\Balance\Dto\Result\BalanceResultDto;
use Rebit\Wallet\Domain\Balance\Entity\Balance;
use Rebit\Wallet\Domain\Balance\Repository\BalanceRepository;

final readonly class GetBalancesUseCase
{
    public function __construct(
        private BalanceRepository $balanceRepository,
        private CurrencyQueryInterface $currencyQuery,
        private CurrencyRubRateQueryInterface $currencyRubRateQuery,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId): BalanceListResultDto
    {
        $collection = $this->balanceRepository->findByUserId($userId);
        $items = $collection->getAll();

        if ([] === $items) {
            return new BalanceListResultDto(balances: []);
        }

        /** @var array<int, string> $currencyCodeMap currencyId → code */
        $currencyCodeMap = [];
        /** @var array<string, ?float> $rubRateMap currencyCode → rubRate */
        $rubRateMap = [];

        foreach ($items as $balance) {
            $currencyId = $balance->getUfCurrencyId();
            if (!isset($currencyCodeMap[$currencyId])) {
                $currencyCodeMap[$currencyId] = $this->currencyQuery->findCodeById($currencyId)
                    ?? "CUR_{$currencyId}";
            }

            $code = $currencyCodeMap[$currencyId];
            if (!array_key_exists($code, $rubRateMap)) {
                $rubRateMap[$code] = $this->currencyRubRateQuery->findApproximateRubRateByCurrencyCode($code);
            }
        }

        $totalRubEquivalent = 0.0;
        $hasAnyRubEquivalent = false;

        $balances = array_map(
            static function(Balance $balance) use ($currencyCodeMap, $rubRateMap, &$totalRubEquivalent, &$hasAnyRubEquivalent): BalanceResultDto {
                $currencyCode = $currencyCodeMap[$balance->getUfCurrencyId()];
                $rubRate = $rubRateMap[$currencyCode];
                $rubEquivalent = null;

                if (null !== $rubRate && 0.0 < $rubRate) {
                    $rubEquivalent = $balance->getUfTotal() * $rubRate;
                    $totalRubEquivalent += $rubEquivalent;
                    $hasAnyRubEquivalent = true;
                }

                return new BalanceResultDto(
                    id: $balance->getId(),
                    userId: $balance->getUfUserId(),
                    currencyId: $balance->getUfCurrencyId(),
                    currency: $currencyCode,
                    available: $balance->getUfAvailable(),
                    locked: $balance->getUfLocked(),
                    total: $balance->getUfTotal(),
                    rubRate: $rubRate,
                    rubEquivalent: $rubEquivalent,
                    syncedAt: $balance->getUfSyncedAt()?->format('c'),
                );
            },
            $items,
        );

        return new BalanceListResultDto(
            balances: $balances,
            totalRubEquivalent: $hasAnyRubEquivalent ? $totalRubEquivalent : null,
        );
    }
}
