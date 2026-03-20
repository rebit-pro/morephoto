<?php

declare(strict_types=1);

namespace Rebit\Share\Domain\File\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class UploadFileResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public string $name,
        public int $size,
        public string $type,
        public string $src,
    ) {}
}
