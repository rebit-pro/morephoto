<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Rebit\Exchange\Application\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Выпуск активов (подтверждение получения оплаты). POST /v5/p2p/order/finish
 */
final readonly class ConfirmReceiptUseCase
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

        if ($trade->getUfSellerUserId() !== $userId) {
            throw new HttpException('Только продавец может подтвердить получение оплаты', 403);
        }

        $this->bybitGateway->releaseAssets(
            $userId,
            $trade->getUfBybitOrderId(),
        );

        $trade->setUfStatus('completed');
        $trade->setUfCompletedAt(new \Bitrix\Main\Type\DateTime());
        $this->tradeRepository->save($trade);

        return ListTradesUseCase::toResultDto($trade);
    }
}
