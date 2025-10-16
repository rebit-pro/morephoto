<?php

declare(strict_types=1);

namespace api\src\Auth\Service;

use api\src\Auth\Entity\User\Email;
use api\src\Auth\Entity\User\Token;

interface JoinConfirmationSender
{
    public function send(Email $email, Token $token): void;
}
