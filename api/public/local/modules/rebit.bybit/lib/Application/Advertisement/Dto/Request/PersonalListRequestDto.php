<?php

declare(strict_types=1);

namespace Rebit\Bybit\Application\Advertisement\Dto\Request;

use Rebit\Share\Shared\Interface\RequestDtoInterface;

final class PersonalListRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly ?string $itemId = null,
        /** 1: Sold Out; 2: Available */
        public readonly ?string $status = null,
        /** 0: buy; 1: sell */
        public readonly ?string $side = null,
        public readonly ?string $tokenId = null,
        public readonly string $page = '1',
        public readonly string $size = '10',
        public readonly ?string $currencyId = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $params = [
            'page' => $this->page,
            'size' => $this->size,
        ];

        if (null !== $this->itemId) {
            $params['itemId'] = $this->itemId;
        }
        if (null !== $this->status) {
            $params['status'] = $this->status;
        }
        if (null !== $this->side) {
            $params['side'] = $this->side;
        }
        if (null !== $this->tokenId) {
            $params['tokenId'] = $this->tokenId;
        }
        if (null !== $this->currencyId) {
            $params['currencyId'] = $this->currencyId;
        }

        return $params;
    }
}

