<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\TradeChat\Dto\Request\SendMessageRequestDto;
use Rebit\Exchange\Application\TradeChat\UseCase\GetChatHistoryUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\SendMessageUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final class TradeChatController extends BaseExchangeController
{
    public function __construct(
        private readonly GetChatHistoryUseCase $getChatHistoryUseCase,
        private readonly SendMessageUseCase $sendMessageUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/exchange/trades/{tradeId}/chat
     * @throws HttpException
     * @throws RepositoryException
     */
    public function historyAction(int $tradeId): ControllerJson
    {
        return $this->json(
            $this->getChatHistoryUseCase->execute($tradeId, $this->getAuthUserId()),
        );
    }

    /**
     * POST /api/v1/exchange/trades/{tradeId}/chat
     * @throws HttpException
     * @throws RepositoryException
     */
    public function sendAction(SendMessageRequestDto $dto): ControllerJson
    {
        return $this->json(
            $this->sendMessageUseCase->execute($dto, $this->getAuthUserId()),
        );
    }
}
