<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
        #[Assert\Positive(message: 'id должен быть положительным числом.')]
        public int $id,
        #[Assert\NotBlank(message: 'Название скрипта обязательно.')]
        public string $name,
        public bool $isActive,
        #[Assert\NotBlank(message: 'Скрипт должен содержать хотя бы один шаг.')]
        public array $steps = [],
    ) {}
}
