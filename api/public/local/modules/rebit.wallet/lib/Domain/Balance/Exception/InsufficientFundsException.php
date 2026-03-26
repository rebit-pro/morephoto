<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Balance\Exception;

/**
 * Недостаточно доступных средств для блокировки.
 */
final class InsufficientFundsException extends \DomainException
{
    public function __construct(float $available, float $amount)
    {
        parent::__construct(
            sprintf(
                'Недостаточно средств: доступно %.8f, запрошено %.8f',
                $available,
                $amount,
            ),
        );
    }
}
