<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class UploadTradeChatFileResultDto implements ResultDtoInterface
{
    public function __construct(
        public string $fileName,
        public string $fileUrl,
        public string $contentType,
        public ?string $providerType = null,
    ) {}
}
