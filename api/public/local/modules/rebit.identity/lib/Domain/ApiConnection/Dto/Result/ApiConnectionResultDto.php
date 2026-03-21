<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Dto\Result;

use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionModeEnum;
use Rebit\Identity\Domain\ApiConnection\Enum\ConnectionStatusEnum;
use Rebit\Share\Application\Interface\ResultDtoInterface;

final readonly class ApiConnectionResultDto implements ResultDtoInterface
{
    public function __construct(
        public int $id,
        public int $userId,
        public ConnectionStatusEnum $status,
        public ConnectionModeEnum $mode,
        public string $maskedApiKey,
        public string $createdAt,
        public ?string $verifiedAt,
    ) {}
}
