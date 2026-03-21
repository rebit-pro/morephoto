<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;

final class ConnectApiRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly string $apiKey,
        public readonly string $apiSecret,
        public readonly string $mode,
    ) {}
}
