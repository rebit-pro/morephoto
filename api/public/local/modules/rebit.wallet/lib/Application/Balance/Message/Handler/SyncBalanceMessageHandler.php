<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Balance\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Share\Application\Contract\Wallet\Message\SyncBalanceMessage;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;
use Rebit\Wallet\Application\Balance\UseCase\SyncBalancesUseCase;

final readonly class SyncBalanceMessageHandler
{
    public function __construct(
        private SyncBalancesUseCase $syncBalancesUseCase,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function __invoke(SyncBalanceMessage $message): void
    {
        if (null !== $message->currency && '' !== $message->currency) {
            $this->logger->info('Запрошена точечная синхронизация валюты, выполняется полный sync пользователя', [
                'userId' => $message->userId,
                'currency' => $message->currency,
            ]);
        }

        try {
            $result = $this->syncBalancesUseCase->execute($message->userId);
        } catch (HttpException $exception) {
            if (400 === $exception->getCode()) {
                $this->logger->warning('SyncBalanceMessage пропущено: нет активного подключения к Bybit', [
                    'userId' => $message->userId,
                    'currency' => $message->currency,
                ]);

                return;
            }

            throw $exception;
        }

        $this->logger->info('SyncBalanceMessage получено', [
            'userId' => $message->userId,
            'currency' => $message->currency,
            'balancesCount' => count($result->balances),
        ]);
    }
}
