<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Entity\Table;

use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnection;
use Rebit\Identity\Domain\ApiConnection\Entity\ApiConnectionCollection;

/**
 * DataManager для HL-блока RebitApiConnection.
 *
 * Регистрируется через compileEntity при первом обращении.
 * Переопределяет getObjectClass / getCollectionClass для
 * подмены стандартных EO_ на доменные Entity / Collection.
 *
 * @todo Наследовать от скомпилированного DataManager после создания HL-блока.
 */
final class ApiConnectionTable // extends compiled HL DataManager
{
    public static function getObjectClass(): string
    {
        return ApiConnection::class;
    }

    public static function getCollectionClass(): string
    {
        return ApiConnectionCollection::class;
    }
}
