<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\ExecuteChatScriptUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\GetChatHistoryUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\ProcessPendingChatScriptsUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\SendMessageUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\SyncChatMessagesUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\UploadTradeChatFileUseCase;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Exchange\Infrastructure\Bitrix\TradeChatUploadFileLocator;
use Rebit\Exchange\Infrastructure\Bybit\BybitChatGateway;
use Rebit\Exchange\Presentation\Command\ExecuteChatScriptsCommand;
use Rebit\Exchange\Presentation\Controller\TradeChatController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Domain\File\Service\UploadedFileOwnershipService;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    TradeMessageRepository::class => [
        'className' => TradeMessageRepository::class,
    ],
    TradeChatUploadFileLocator::class => [
        'constructor' => static function(): TradeChatUploadFileLocator {
            return new TradeChatUploadFileLocator(
                ServiceLocator::getInstance()->get(UploadedFileOwnershipService::class),
            );
        },
    ],
    BybitChatGatewayInterface::class => [
        'constructor' => static function(): BybitChatGatewayInterface {
            $sl = ServiceLocator::getInstance();

            return new BybitChatGateway(
                $sl->get(BybitConnectionResolverInterface::class),
                $sl->get(BybitClientInterface::class),
            );
        },
    ],
    SyncChatMessagesUseCase::class => [
        'className' => SyncChatMessagesUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeMessageRepository::class),
            ServiceLocator::getInstance()->get(BybitChatGatewayInterface::class),
        ],
    ],
    GetChatHistoryUseCase::class => [
        'className' => GetChatHistoryUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeMessageRepository::class),
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(SyncChatMessagesUseCase::class),
        ],
    ],
    SendMessageUseCase::class => [
        'className' => SendMessageUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeMessageRepository::class),
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(BybitChatGatewayInterface::class),
        ],
    ],
    UploadTradeChatFileUseCase::class => [
        'className' => UploadTradeChatFileUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(BybitChatGatewayInterface::class),
            ServiceLocator::getInstance()->get(TradeChatUploadFileLocator::class),
        ],
    ],
    ExecuteChatScriptUseCase::class => [
        'className' => ExecuteChatScriptUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ChatScriptRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(TradeMessageRepository::class),
            ServiceLocator::getInstance()->get(BybitChatGatewayInterface::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    ChatScriptExecutionRepository::class => [
        'className' => ChatScriptExecutionRepository::class,
    ],
    ProcessPendingChatScriptsUseCase::class => [
        'className' => ProcessPendingChatScriptsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ChatScriptExecutionRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(TradeMessageRepository::class),
            ServiceLocator::getInstance()->get(BybitChatGatewayInterface::class),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    ExecuteChatScriptsCommand::class => [
        'className' => ExecuteChatScriptsCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ProcessPendingChatScriptsUseCase::class),
        ],
    ],
    TradeChatController::class => [
        'className' => TradeChatController::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(GetChatHistoryUseCase::class),
            ServiceLocator::getInstance()->get(SendMessageUseCase::class),
            ServiceLocator::getInstance()->get(UploadTradeChatFileUseCase::class),
        ],
    ],
];
