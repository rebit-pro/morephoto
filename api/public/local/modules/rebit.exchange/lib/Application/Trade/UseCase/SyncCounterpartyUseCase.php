<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Port\BybitCounterpartyGatewayInterface;
use Rebit\Exchange\Domain\Counterparty\Repository\CounterpartyRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class SyncCounterpartyUseCase
{
    public function __construct(
        private BybitCounterpartyGatewayInterface $bybitCounterpartyGateway,
        private CounterpartyRepository $counterpartyRepository,
        private TradeRepository $tradeRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param array<string, mixed> $orderInfo
     *
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(Trade $trade, array $orderInfo): void
    {
        $targetUserId = (string)($orderInfo['targetUserId'] ?? '');
        if ('' === $targetUserId) {
            $this->logger->warning('SyncCounterparty пропущен: targetUserId отсутствует в order info', [
                'tradeId' => $trade->getId(),
                'bybitOrderId' => $trade->getUfBybitOrderId(),
            ]);

            return;
        }

        $currentUserId = $this->resolveCurrentLocalUserId($trade);
        if (0 >= $currentUserId) {
            $this->logger->warning('SyncCounterparty пропущен: не удалось определить локального пользователя сделки', [
                'tradeId' => $trade->getId(),
                'side' => $trade->getUfSide(),
                'buyerUserId' => $trade->getUfBuyerUserId(),
                'sellerUserId' => $trade->getUfSellerUserId(),
            ]);

            return;
        }

        $profile = $this->bybitCounterpartyGateway->fetchProfile(
            userId: $currentUserId,
            originalUid: $targetUserId,
            orderId: $trade->getUfBybitOrderId(),
        );

        $counterpartyUserId = $this->counterpartyRepository->upsert($profile);

        if ('buy' === $trade->getUfSide()) {
            $trade->setUfBuyerUserId($currentUserId);
            $trade->setUfSellerUserId($counterpartyUserId);
        } else {
            $trade->setUfSellerUserId($currentUserId);
            $trade->setUfBuyerUserId($counterpartyUserId);
        }

        if ('' === $trade->getUfCounterpartyName() && '' !== $profile['nickName']) {
            $trade->setUfCounterpartyName((string)$profile['nickName']);
        }

        $this->tradeRepository->save($trade);

        $this->logger->info('Контрагент синхронизирован и привязан к сделке', [
            'tradeId' => $trade->getId(),
            'bybitOrderId' => $trade->getUfBybitOrderId(),
            'counterpartyUserId' => $counterpartyUserId,
            'targetUserId' => $targetUserId,
            'buyerUserId' => $trade->getUfBuyerUserId(),
            'sellerUserId' => $trade->getUfSellerUserId(),
        ]);
    }

    private function resolveCurrentLocalUserId(Trade $trade): int
    {
        return match ($trade->getUfSide()) {
            'buy' => 0 < $trade->getUfBuyerUserId() ? $trade->getUfBuyerUserId() : $trade->getUfSellerUserId(),
            'sell' => 0 < $trade->getUfSellerUserId() ? $trade->getUfSellerUserId() : $trade->getUfBuyerUserId(),
            default => 0,
        };
    }
}
