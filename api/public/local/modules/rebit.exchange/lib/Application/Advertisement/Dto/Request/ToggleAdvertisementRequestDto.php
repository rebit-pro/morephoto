<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ToggleAdvertisementRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'status обязателен.')]
        #[Assert\Choice(choices: ['active', 'paused'], message: 'status должен быть active или paused.')]
        public string $status,
    ) {}
}
