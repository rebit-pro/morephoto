<?php

declare(strict_types=1);

namespace Rebit\Auth\Domain\User\Service;

use Random\RandomException;

interface TokenGeneratorInterface
{
    /**
     * @throws RandomException
     */
    public function generate(): string;
}
