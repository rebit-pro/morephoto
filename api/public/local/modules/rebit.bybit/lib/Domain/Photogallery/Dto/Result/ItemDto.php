<?php

declare(strict_types=1);

namespace Rebit\PhotoOrder\Domain\Photogallery\Dto\Result;

final class ItemDto
{
    public function __construct(
        public string $src,
    ) {}
}
