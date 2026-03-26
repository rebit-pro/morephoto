<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SendMessageRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Positive(message: 'tradeId должен быть положительным числом.')]
        public int $tradeId,
        #[Assert\NotBlank(message: 'Сообщение не может быть пустым.')]
        public string $message,
        #[Assert\Choice(choices: ['str', 'pic', 'pdf', 'video'], message: 'Некорректный тип контента.')]
        public string $contentType = 'str',
        public ?string $fileName = null,
    ) {}
}
