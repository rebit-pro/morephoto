<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Bitrix\Main\Type\DateTime;
use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class EnrichTradeFromBybitUseCase
{
    public function __construct(
        private BybitTradeGatewayInterface $bybitTradeGateway,
        private AdvertisementRepository $advertisementRepository,
        private PaymentMethodRepository $paymentMethodRepository,
        private TradeRepository $tradeRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(Trade $trade): array
    {
        $userId = match ($trade->getUfSide()) {
            'buy' => 0 < $trade->getUfBuyerUserId() ? $trade->getUfBuyerUserId() : $trade->getUfSellerUserId(),
            'sell' => 0 < $trade->getUfSellerUserId() ? $trade->getUfSellerUserId() : $trade->getUfBuyerUserId(),
            default => 0,
        };

        if (0 >= $userId) {
            $this->logger->warning('Обогащение сделки пропущено: локальный пользователь сделки не определён', [
                'tradeId' => $trade->getId(),
                'bybitOrderId' => $trade->getUfBybitOrderId(),
            ]);

            return [];
        }

        $orderInfo = $this->bybitTradeGateway->fetchOrderInfo($userId, $trade->getUfBybitOrderId());

        $bybitAdId = (string)($orderInfo['itemId'] ?? '');
        if ('' !== $bybitAdId) {
            $advertisement = $this->advertisementRepository->findByBybitAdId($bybitAdId);
            if (null !== $advertisement) {
                $trade->setUfAdvertisementId($advertisement->getId());
            }
        }

        $paymentType = (int)($orderInfo['paymentType'] ?? 0);
        if (0 < $paymentType) {
            $paymentMethod = $this->paymentMethodRepository->findByBybitId($paymentType);
            if (null !== $paymentMethod) {
                $trade->setUfPaymentMethodId($paymentMethod->getId());
            }
        }

        $quantity = (float)($orderInfo['quantity'] ?? 0);
        if (0.0 < $quantity) {
            $trade->setUfQuantity($quantity);
        }

        $remark = (string)($orderInfo['remark'] ?? '');
        if ('' !== $remark) {
            $trade->setUfComment($remark);
        }

        $paymentDeadlineSeconds = (int)($orderInfo['transferLastSeconds'] ?? 0);
        if (0 < $paymentDeadlineSeconds) {
            $trade->setUfPaymentDeadline((new DateTime())->add('+' . $paymentDeadlineSeconds . ' seconds'));
        }

        $counterpartyName = (string)($orderInfo['targetNickName'] ?? '');
        if ('' !== $counterpartyName) {
            $trade->setUfCounterpartyName($counterpartyName);
        }

        $this->tradeRepository->save($trade);

        $this->logger->info('Сделка обогащена деталями Bybit', [
            'tradeId' => $trade->getId(),
            'bybitOrderId' => $trade->getUfBybitOrderId(),
            'advertisementId' => $trade->getUfAdvertisementId(),
            'paymentMethodId' => $trade->getUfPaymentMethodId(),
        ]);

        return $orderInfo;
    }
}
