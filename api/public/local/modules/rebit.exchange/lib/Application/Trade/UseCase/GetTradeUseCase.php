<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Rebit\Exchange\Application\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Application\Trade\Mapper\TradeResultDtoMapper;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;

/**
 * Получение деталей конкретной сделки. При необходимости обновляет из Bybit.
 */
final readonly class GetTradeUseCase
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private BybitTradeGatewayInterface $bybitGateway,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $tradeId, int $userId): TradeResultDto
    {
        $trade = $this->tradeRepository->findById($tradeId);

        if (null === $trade) {
            throw new EntityNotFoundException('Сделка не найдена');
        }

        if ($trade->getUfBuyerUserId() !== $userId && $trade->getUfSellerUserId() !== $userId) {
            throw new HttpException('Нет доступа к этой сделке', 403);
        }

        // Обновляем статус из Bybit для активных сделок
        $bybitOrderId = $trade->getUfBybitOrderId();
        if ('' !== $bybitOrderId && $this->isActiveStatus($trade->getUfStatus())) {
            try {
                $bybitData = $this->bybitGateway->fetchOrderInfo($userId, $bybitOrderId);
                $bybitStatus = (int)($bybitData['status'] ?? 0);

                if (0 !== $bybitStatus && $bybitStatus !== $trade->getUfBybitStatus()) {
                    $trade->setUfBybitStatus($bybitStatus);
                    $trade->setUfStatus(
                        TradeStatusEnum::fromBybit($bybitStatus)->value,
                    );
                    $this->tradeRepository->save($trade);
                }
            } catch (HttpException) {
                // Не блокируем отображение сделки при ошибке Bybit
            }
        }

        return TradeResultDtoMapper::fromEntity($trade);
    }

    private function isActiveStatus(string $status): bool
    {
        return in_array($status, [
            'pending_payment',
            'payment_sent',
            'payment_confirmed',
            'disputed',
        ], true);
    }
}
