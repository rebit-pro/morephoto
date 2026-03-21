<?php

declare(strict_types=1);

namespace Rebit\Auth\Application\Auth\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final class LoginRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}
}
