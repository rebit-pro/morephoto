<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\Message;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;

final readonly class SyncIdentityMessage extends AbstractMessage
{
    public function __construct(
        public int $userId,
    ) {
        parent::__construct();
    }
}
