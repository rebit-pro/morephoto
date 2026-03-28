<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Message;

use Rebit\Share\Application\Contract\Wallet\Message\SyncBalanceMessage;

class_alias(
    SyncBalanceMessage::class,
    __NAMESPACE__ . '\SyncBalanceMessage',
);
