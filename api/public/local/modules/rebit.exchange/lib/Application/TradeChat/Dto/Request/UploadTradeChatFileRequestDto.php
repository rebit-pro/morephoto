<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UploadTradeChatFileRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Positive(message: 'tradeId должен быть положительным числом.')]
        public int $tradeId,
        #[Assert\Positive(message: 'fileId должен быть положительным числом.')]
        public int $fileId,
    ) {}
}
