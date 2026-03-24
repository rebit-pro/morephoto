<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final class UpdateChatScriptRequestDto implements RequestDtoInterface
{
    /**
     * @param array<int, array{
     *     sort: int,
     *     message: string,
     *     delaySeconds: int,
     * }> $steps
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly bool $isActive,
        public readonly array $steps = [],
    ) {}
}
