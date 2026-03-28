<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateAdvertisementRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Positive(message: 'currencyPairId должен быть положительным числом.')]
        public int $currencyPairId,
        #[Assert\NotBlank(message: 'side обязателен.')]
        #[Assert\Choice(choices: ['buy', 'sell'], message: 'side должен быть buy или sell.')]
        public string $side,
        #[Assert\NotBlank(message: 'priceType обязателен.')]
        #[Assert\Choice(choices: ['fixed', 'floating'], message: 'priceType должен быть fixed или floating.')]
        public string $priceType,
        #[Assert\NotBlank(message: 'price обязателен.')]
        public string $price,
        public ?string $premium,
        #[Assert\NotBlank(message: 'quantity обязателен.')]
        public string $quantity,
        #[Assert\NotBlank(message: 'minAmount обязателен.')]
        public string $minAmount,
        #[Assert\NotBlank(message: 'maxAmount обязателен.')]
        public string $maxAmount,
        /**
         * @var array<int, string>
         */
        #[Assert\NotBlank(message: 'Необходимо указать хотя бы один способ оплаты.')]
        public array $paymentMethodIds,
        #[Assert\Positive(message: 'paymentPeriod должен быть положительным числом.')]
        public int $paymentPeriod,
        public string $conditions = '',
        #[Assert\Positive(message: 'chatScriptId должен быть положительным числом.')]
        public ?int $chatScriptId = null,
        /**
         * @var array<string, string>
         */
        public array $tradingPreferenceSet = [],
    ) {}
}
