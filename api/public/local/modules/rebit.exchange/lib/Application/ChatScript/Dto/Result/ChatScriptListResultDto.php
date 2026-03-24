<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class ChatScriptListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, ChatScriptResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
