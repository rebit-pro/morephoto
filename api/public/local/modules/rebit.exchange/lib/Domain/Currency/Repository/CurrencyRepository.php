<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Currency\Repository;

use Rebit\Exchange\Domain\Currency\Entity\Currency;
use Rebit\Exchange\Domain\Currency\Entity\CurrencyCollection;
use Rebit\Exchange\Domain\Currency\Entity\Table\CurrencyTable;
use Rebit\Share\Shared\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class CurrencyRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findActive(): CurrencyCollection
    {
        return $this->query(
            fn(): CurrencyCollection => CurrencyTable::query()
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
    public function findByCode(string $code): ?Currency
    {
        return $this->query(
            fn(): ?Currency => CurrencyTable::query()
                ->setSelect(['*'])
                ->where('UF_CODE', $code)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?Currency
    {
        return $this->query(
            fn(): ?Currency => CurrencyTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }
}
