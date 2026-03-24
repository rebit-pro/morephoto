<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\UseCase;

use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyListResultDto;
use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyResultDto;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class GetCurrenciesUseCase
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(): CurrencyListResultDto
    {
        $currencies = $this->currencyRepository->findActive();

        $items = [];
        foreach ($currencies as $currency) {
            $items[] = new CurrencyResultDto(
                id: $currency->getId(),
                code: $currency->getUfCode(),
                name: $currency->getUfName(),
                type: $currency->getUfType(),
                decimals: $currency->getUfDecimals(),
                sort: $currency->getUfSort(),
            );
        }

        return new CurrencyListResultDto($items);
    }
}
