<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ConnectApiRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'API ключ обязателен.')]
        public string $apiKey,
        #[Assert\NotBlank(message: 'Secret ключ обязателен.')]
        public string $secretKey,
        #[Assert\NotBlank(message: 'Режим подключения обязателен.')]
        #[Assert\Choice(choices: ['testnet', 'mainnet'], message: 'mode должен быть testnet или mainnet.')]
        public string $mode,
    ) {}
}
