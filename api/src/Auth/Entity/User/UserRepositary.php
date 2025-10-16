<?php

declare(strict_types=1);

namespace api\src\Auth\Entity\User;

use api\src\Auth\Entity\User\Email;
use api\src\Auth\Entity\User\User;

interface UserRepositary
{
    public function add(User $user): void;
    public function hasByEmail(Email $email): bool;
}
