<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Wallet;

use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;

/**
 * Контракт publisher'а синхронизации баланса.
 */
interface BalanceSyncPublisherInterface extends MessagePublisherInterface {}
