<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Result;

use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class AdvertisementListResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, AdvertisementResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}
