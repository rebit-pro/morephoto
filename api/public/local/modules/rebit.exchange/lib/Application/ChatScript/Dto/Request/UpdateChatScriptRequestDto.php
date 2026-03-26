<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final readonly class UpdateChatScriptRequestDto implements RequestDtoInterface
{
    /**
     * @param array<int, array{
     *     sort: int,
     *     message: string,
     *     delaySeconds: int,
     * }> $steps
     */
    public function __construct(
        public int $id,
        public string $name,
        public bool $isActive,
        public array $steps = [],
    ) {}
}
