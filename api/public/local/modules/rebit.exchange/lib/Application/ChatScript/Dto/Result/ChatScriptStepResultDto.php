<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class ChatScriptStepResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public int $sort,
        public string $message,
        public int $delaySeconds,
    ) {}
}
