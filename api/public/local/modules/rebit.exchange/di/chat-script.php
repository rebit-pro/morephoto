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
        'className' => ListChatScriptsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ChatScriptRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
        ],
    ],
    CreateChatScriptUseCase::class => [
        'className' => CreateChatScriptUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ChatScriptRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
        ],
    ],
    UpdateChatScriptUseCase::class => [
        'className' => UpdateChatScriptUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ChatScriptRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
        ],
    ],
    DeleteChatScriptUseCase::class => [
        'className' => DeleteChatScriptUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ChatScriptRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
            ServiceLocator::getInstance()->get(AdvertisementRepository::class),
        ],
    ],
    ChatScriptController::class => [
        'className' => ChatScriptController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ListChatScriptsUseCase::class),
            ServiceLocator::getInstance()->get(CreateChatScriptUseCase::class),
            ServiceLocator::getInstance()->get(UpdateChatScriptUseCase::class),
            ServiceLocator::getInstance()->get(DeleteChatScriptUseCase::class),
        ],
    ],
];
