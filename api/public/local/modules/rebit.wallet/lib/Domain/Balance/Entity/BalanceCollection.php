<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Entity;

use Rebit\Wallet\Domain\Balance\Entity\Table\EO_Balance_Collection;

/**
 * Коллекция балансов пользователя.
 * Наследует скомпилированный EO_Balance_Collection класс HL-блока.
 */
final class BalanceCollection extends EO_Balance_Collection {}
