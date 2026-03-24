<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Controller;

use Rebit\Exchange\Application\ChatScript\Dto\Request\CreateChatScriptRequestDto;
use Rebit\Exchange\Application\ChatScript\Dto\Request\UpdateChatScriptRequestDto;
use Rebit\Exchange\Application\ChatScript\UseCase\CreateChatScriptUseCase;
use Rebit\Exchange\Application\ChatScript\UseCase\DeleteChatScriptUseCase;
use Rebit\Exchange\Application\ChatScript\UseCase\ListChatScriptsUseCase;
use Rebit\Exchange\Application\ChatScript\UseCase\UpdateChatScriptUseCase;
use Rebit\Exchange\Infrastructure\Controller\BaseExchangeController;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;

final class ChatScriptController extends BaseExchangeController
{
    public function __construct(
        private readonly ListChatScriptsUseCase $listUseCase,
        private readonly CreateChatScriptUseCase $createUseCase,
        private readonly UpdateChatScriptUseCase $updateUseCase,
        private readonly DeleteChatScriptUseCase $deleteUseCase,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v1/exchange/chat-scripts
     */
    public function listAction(): ControllerJson
    {
        return $this->json(
            $this->listUseCase->execute($this->getAuthUserId()),
        );
    }

    /**
     * POST /api/v1/exchange/chat-scripts
     */
    public function createAction(CreateChatScriptRequestDto $dto): ControllerJson
    {
        return $this->json(
            $this->createUseCase->execute($dto, $this->getAuthUserId()),
        );
    }

    /**
     * PATCH /api/v1/exchange/chat-scripts/{id}
     */
    public function updateAction(UpdateChatScriptRequestDto $dto): ControllerJson
    {
        return $this->json(
            $this->updateUseCase->execute($dto, $this->getAuthUserId()),
        );
    }

    /**
     * DELETE /api/v1/exchange/chat-scripts/{id}
     */
    public function deleteAction(int $id): ControllerJson
    {
        $this->deleteUseCase->execute($id, $this->getAuthUserId());

        return $this->json([]);
    }
}
