<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Rebit\Exchange\Application\Trade\Dto\Result\TradeListResultDto;
use Rebit\Exchange\Application\Trade\Mapper\TradeResultDtoMapper;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Получение списка сделок пользователя.
 */
final readonly class ListTradesUseCase
{
    public function __construct(
        private TradeRepository $tradeRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId, ?string $status = null): TradeListResultDto
    {
        $trades = $this->tradeRepository->findByUserId($userId, $status);

        $items = [];
        foreach ($trades as $trade) {
            $items[] = TradeResultDtoMapper::fromEntity($trade);
        }

        return new TradeListResultDto($items);
    }
}
