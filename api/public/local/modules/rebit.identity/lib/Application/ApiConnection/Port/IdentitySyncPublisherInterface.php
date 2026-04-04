<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\Port;

use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;

/**
 * Контракт publisher'а синхронизации идентификации (identitySync).
 */
interface IdentitySyncPublisherInterface extends MessagePublisherInterface {}
