<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Domain\Photogallery\Dto\Request;

use Rebit\Share\Shared\Interface\RequestDtoInterface;

final class PhotogalleryRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $code,
    ) {}
}
