<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\PaymentMethod\Repository;

use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod;
use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection;
use Rebit\Exchange\Domain\PaymentMethod\Entity\Table\PaymentMethodTable;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class PaymentMethodRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findActive(): PaymentMethodCollection
    {
        return $this->query(
            fn(): PaymentMethodCollection => PaymentMethodTable::query()
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
    public function findByCode(string $code): ?PaymentMethod
    {
        return $this->query(
            fn(): ?PaymentMethod => PaymentMethodTable::query()
                ->setSelect(['*'])
                ->where('UF_CODE', $code)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?PaymentMethod
    {
        return $this->query(
            fn(): ?PaymentMethod => PaymentMethodTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }
}
