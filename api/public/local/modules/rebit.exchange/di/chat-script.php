<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\ChatScript\UseCase\CreateChatScriptUseCase;
use Rebit\Exchange\Application\ChatScript\UseCase\DeleteChatScriptUseCase;
use Rebit\Exchange\Application\ChatScript\UseCase\ListChatScriptsUseCase;
use Rebit\Exchange\Application\ChatScript\UseCase\UpdateChatScriptUseCase;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Presentation\Controller\ChatScriptController;

return [
    ChatScriptRepository::class => [
        'className' => ChatScriptRepository::class,
    ],
    ChatScriptStepRepository::class => [
        'className' => ChatScriptStepRepository::class,
    ],
    ListChatScriptsUseCase::class => [
        'constructor' => static function(): ListChatScriptsUseCase {
            $sl = ServiceLocator::getInstance();

            return new ListChatScriptsUseCase(
                $sl->get(ChatScriptRepository::class),
                $sl->get(ChatScriptStepRepository::class),
            );
        },
    ],
    CreateChatScriptUseCase::class => [
        'constructor' => static function(): CreateChatScriptUseCase {
            $sl = ServiceLocator::getInstance();

            return new CreateChatScriptUseCase(
                $sl->get(ChatScriptRepository::class),
                $sl->get(ChatScriptStepRepository::class),
            );
        },
    ],
    UpdateChatScriptUseCase::class => [
        'constructor' => static function(): UpdateChatScriptUseCase {
            $sl = ServiceLocator::getInstance();

            return new UpdateChatScriptUseCase(
                $sl->get(ChatScriptRepository::class),
                $sl->get(ChatScriptStepRepository::class),
            );
        },
    ],
    DeleteChatScriptUseCase::class => [
        'constructor' => static function(): DeleteChatScriptUseCase {
            $sl = ServiceLocator::getInstance();

            return new DeleteChatScriptUseCase(
                $sl->get(ChatScriptRepository::class),
                $sl->get(ChatScriptStepRepository::class),
                $sl->get(AdvertisementRepository::class),
            );
        },
    ],
    ChatScriptController::class => [
        'constructor' => static function(): ChatScriptController {
            $sl = ServiceLocator::getInstance();

            return new ChatScriptController(
                $sl->get(ListChatScriptsUseCase::class),
                $sl->get(CreateChatScriptUseCase::class),
                $sl->get(UpdateChatScriptUseCase::class),
                $sl->get(DeleteChatScriptUseCase::class),
            );
        },
    ],
];
