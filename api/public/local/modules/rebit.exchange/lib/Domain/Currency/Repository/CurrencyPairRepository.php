<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Currency\Repository;

use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection;
use Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyPairTable;
use Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyTable;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class CurrencyPairRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findActive(): CurrencyPairCollection
    {
        return $this->query(
            fn(): CurrencyPairCollection => CurrencyPairTable::query()
                ->setSelect(['*'])
                ->where('UF_IS_ACTIVE', 1)
                ->setOrder(['UF_SORT' => 'ASC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findDefault(): ?CurrencyPair
    {
        return $this->query(
            fn(): ?CurrencyPair => CurrencyPairTable::query()
                ->setSelect(['*'])
                ->where('UF_IS_DEFAULT', 1)
                ->where('UF_IS_ACTIVE', 1)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?CurrencyPair
    {
        return $this->query(
            fn(): ?CurrencyPair => CurrencyPairTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByCode(string $code): ?CurrencyPair
    {
        return $this->query(
            fn(): ?CurrencyPair => CurrencyPairTable::query()
                ->setSelect(['*'])
                ->where('UF_CODE', $code)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * Находит активную валютную пару по символу токена и символу фиата.
     *
     * @throws RepositoryException
     */
    public function findByTokenAndFiat(string $token, string $fiat): ?CurrencyPair
    {
        $tokenCurrency = $this->query(
            fn(): ?object => CurrencyTable::query()
                ->setSelect(['ID'])
                ->where('UF_CODE', $token)
                ->exec()
                ->fetchObject(),
        );
        if (null === $tokenCurrency) {
            return null;
        }
        $fiatCurrency = $this->query(
            fn(): ?object => CurrencyTable::query()
                ->setSelect(['ID'])
                ->where('UF_CODE', $fiat)
                ->exec()
                ->fetchObject(),
        );
        if (null === $fiatCurrency) {
            return null;
        }
        return $this->query(
            fn(): ?CurrencyPair => CurrencyPairTable::query()
                ->setSelect(['*'])
                ->where('UF_TOKEN_CURRENCY_ID', $tokenCurrency->getId())
                ->where('UF_FIAT_CURRENCY_ID', $fiatCurrency->getId())
                ->where('UF_IS_ACTIVE', 1)
                ->exec()
                ->fetchObject(),
        );
    }
}
