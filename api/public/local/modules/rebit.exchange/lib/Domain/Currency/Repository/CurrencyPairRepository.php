<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Currency\Repository;

use Rebit\Exchange\Domain\Currency\Entity\CurrencyPair;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyPairCollection;
use Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyPairTable;
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
}
