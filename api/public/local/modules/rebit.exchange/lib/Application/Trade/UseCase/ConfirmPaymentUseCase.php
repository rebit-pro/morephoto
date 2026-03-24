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
 * Подтверждение оплаты покупателем. POST /v5/p2p/order/pay
 */
final readonly class ConfirmPaymentUseCase
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private BybitTradeGatewayInterface $bybitGateway,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $tradeId, int $userId, string $paymentType, string $paymentId): TradeResultDto
    {
        $trade = $this->tradeRepository->findById($tradeId);

        if (null === $trade) {
            throw new EntityNotFoundException('Сделка не найдена');
        }

        if ($trade->getUfBuyerUserId() !== $userId) {
            throw new HttpException('Только покупатель может подтвердить оплату', 403);
        }

        $this->bybitGateway->confirmPayment(
            $userId,
            $trade->getUfBybitOrderId(),
            $paymentType,
            $paymentId,
        );

        $trade->setUfStatus('payment_sent');
        $trade->setUfPaidAt(new \Bitrix\Main\Type\DateTime());
        $this->tradeRepository->save($trade);

        return ListTradesUseCase::toResultDto($trade);
    }
}
