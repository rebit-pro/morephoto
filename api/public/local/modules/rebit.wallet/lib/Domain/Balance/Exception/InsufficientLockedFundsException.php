<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Exception;

/**
 * Невозможно разблокировать: заблокировано меньше, чем запрошено.
 */
final class InsufficientLockedFundsException extends \DomainException
{
    public function __construct(float $locked, float $amount)
    {
        parent::__construct(
            sprintf(
                'Невозможно разблокировать: заблокировано %.8f, запрошено %.8f',
                $locked,
                $amount,
            ),
        );
    }
}
