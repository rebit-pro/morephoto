<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Wallet\Message;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;

final readonly class SyncBalanceMessage extends AbstractMessage
{
    public function __construct(
        public int $userId,
        public ?string $currency = null,
    ) {
        parent::__construct();
    }
}
