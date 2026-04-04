<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\Port;

use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;

/**
 * Контракт publisher'а событий сделок (tradeEvent).
 */
interface TradeEventPublisherInterface extends MessagePublisherInterface {}
