<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Currency\UseCase;

use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyPairListResultDto;
use Rebit\Exchange\Application\Currency\Dto\Result\CurrencyPairResultDto;
use Rebit\Exchange\Domain\Currency\Repository\CurrencyPairRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class GetCurrencyPairsUseCase
{
    public function __construct(
        private CurrencyPairRepository $currencyPairRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(): CurrencyPairListResultDto
    {
        $pairs = $this->currencyPairRepository->findActive();

        $items = [];
        foreach ($pairs as $pair) {
            $items[] = new CurrencyPairResultDto(
                id: $pair->getId(),
                code: $pair->getUfCode(),
                tokenCurrencyId: $pair->getUfTokenCurrencyId(),
                fiatCurrencyId: $pair->getUfFiatCurrencyId(),
                isDefault: (bool)$pair->getUfIsDefault(),
                sort: $pair->getUfSort(),
            );
        }

        return new CurrencyPairListResultDto($items);
    }
}
