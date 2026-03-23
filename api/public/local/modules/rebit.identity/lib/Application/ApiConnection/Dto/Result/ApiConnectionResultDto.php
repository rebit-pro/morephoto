<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\Dto\Result;

use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class ApiConnectionResultDto implements ResultDtoInterface
{
    public function __construct(
        public bool $connected,
        public ?ConnectionStatusEnum $status = null,
        public ?ConnectionModeEnum $mode = null,
        public ?int $id = null,
        public ?int $userId = null,
        public ?string $maskedApiKey = null,
        public ?string $createdAt = null,
        public ?string $verifiedAt = null,
    ) {}
}
