<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\UseCase;

use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyPairListResultDto;
use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyPairResultDto;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class GetCurrencyPairsUseCase
{
    public function __construct(
        private CurrencyPairRepository $currencyPairRepository,
        private CurrencyRepository $currencyRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(): CurrencyPairListResultDto
    {
        $currencies = $this->currencyRepository->findActive();
        $currencyCodeMap = [];
        foreach ($currencies as $currency) {
            $currencyCodeMap[$currency->getId()] = $currency->getUfCode();
        }

        $pairs = $this->currencyPairRepository->findActive();

        $items = [];
        foreach ($pairs as $pair) {
            $tokenCode = $currencyCodeMap[$pair->getUfTokenCurrencyId()] ?? '';
            $fiatCode = $currencyCodeMap[$pair->getUfFiatCurrencyId()] ?? '';

            $items[] = new CurrencyPairResultDto(
                id: $pair->getId(),
                code: $pair->getUfCode(),
                tokenCurrencyId: $pair->getUfTokenCurrencyId(),
                fiatCurrencyId: $pair->getUfFiatCurrencyId(),
                tokenCode: $tokenCode,
                fiatCode: $fiatCode,
                isDefault: (bool)$pair->getUfIsDefault(),
                sort: $pair->getUfSort(),
            );
        }

        return new CurrencyPairListResultDto($items);
    }
}
