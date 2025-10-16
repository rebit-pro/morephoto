<?php

declare(strict_types=1);

namespace api\src\Auth\Command\JoinByEmail\Request;

final class Command
{
    public string $email = '';
    public string $password = '';
}
