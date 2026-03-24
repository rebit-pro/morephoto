<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class ChatScriptResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, ChatScriptStepResultDto> $steps
     */
    public function __construct(
        public int $id,
        public string $name,
        public bool $isActive,
        public ?string $createdAt,
        public ?string $updatedAt,
        public array $steps = [],
    ) {}
}
