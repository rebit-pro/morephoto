<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Transaction\Entity;

use Rebit\Wallet\Domain\Transaction\Entity\Table\EO_Transaction_Collection;

/**
 * Коллекция транзакций.
 * Наследует скомпилированный EO_Transaction_Collection класс HL-блока.
 */
final class TransactionCollection extends EO_Transaction_Collection {}
