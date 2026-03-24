<?php
declare(strict_types=1);
namespace Rebit\Exchange\Application\OrderBook\Dto\Result;
use Rebit\Share\Application\Interface\ResultDtoInterface;
final readonly class OrderBookBothSidesResultDto implements ResultDtoInterface
{
    /**
     * @param array<int, OrderBookEntryResultDto> $buy
     * @param array<int, OrderBookEntryResultDto> $sell
     */
    public function __construct(
        public array $buy,
        public array $sell,
    ) {}
}
