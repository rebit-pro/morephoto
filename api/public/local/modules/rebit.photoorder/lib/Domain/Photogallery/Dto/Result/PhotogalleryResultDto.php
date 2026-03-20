<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Domain\Photogallery\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final class PhotogalleryResultDto implements ResultDtoInterface
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        /** @var ItemDto[] $photos */
        public array $photos,
    ) {}
}
