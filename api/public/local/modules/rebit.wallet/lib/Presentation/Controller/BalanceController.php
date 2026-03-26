<?php

declare(strict_types=1);

namespace Rebit\Wallet\Presentation\Controller;

use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Wallet\Application\Balance\UseCase\GetBalancesUseCase;
use Rebit\Wallet\Application\Balance\UseCase\SyncBalancesUseCase;
use Rebit\Wallet\Infrastructure\Controller\BaseWalletController;

final class BalanceController extends BaseWalletController
{
    public function __construct(
        private readonly GetBalancesUseCase $getBalancesUseCase,
        private readonly SyncBalancesUseCase $syncBalancesUseCase,
    ) {}

    /**
     * GET /api/v1/wallet/balances
     */
    public function listAction(): ControllerJson
    {
        return $this->json(
            $this->getBalancesUseCase->execute($this->getAuthUserId()),
        );
    }

    /**
     * POST /api/v1/wallet/balances/sync
     *
     * @throws HttpException
     * @throws \Exception
     */
    public function syncAction(): ControllerJson
    {
        return $this->json(
            $this->syncBalancesUseCase->execute($this->getAuthUserId()),
        );
    }
}
