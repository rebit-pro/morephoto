<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Port;

use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;

/**
 * Контракт publisher'а шагов чат-скриптов (chatScriptStep).
 */
interface ChatScriptStepPublisherInterface extends MessagePublisherInterface {}
