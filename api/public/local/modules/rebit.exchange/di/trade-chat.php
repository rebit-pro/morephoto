<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\TradeChat\Message\Handler\ExecuteChatScriptStepMessageHandler;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\ConsumeChatScriptStepsUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\ExecuteQueuedChatScriptStepUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\ExecuteChatScriptUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\GetChatHistoryUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\ProcessPendingChatScriptsUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\SendMessageUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\StartTradeChatScriptUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\SyncChatMessagesUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\UploadTradeChatFileUseCase;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Exchange\Infrastructure\Bitrix\TradeChatUploadFileLocator;
use Rebit\Exchange\Infrastructure\Bybit\BybitChatGateway;
use Rebit\Exchange\Infrastructure\TradeChat\Messenger\ChatScriptMessengerFactory;
use Rebit\Exchange\Presentation\Command\ExecuteChatScriptsCommand;
use Rebit\Exchange\Presentation\Command\TradeChat\ChatScriptStepConsumerCommand;
use Rebit\Exchange\Presentation\Command\TradeChat\TestChatScriptStepCommand;
use Rebit\Exchange\Presentation\Controller\TradeChatController;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Domain\File\Service\UploadedFileOwnershipService;
use Rebit\Share\Infrastructure\Messenger\AmqpConnectionFactory;
use Rebit\Share\Infrastructure\Messenger\ConsumerRunnerInterface;
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
    ExecuteChatScriptStepMessageHandler::class => [
        'className' => ExecuteChatScriptStepMessageHandler::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ExecuteQueuedChatScriptStepUseCase::class),
            Log::channel(LogChannelEnum::exchange),
        ],
    ],
    'exchange.chat_script_step.publisher' => [
        'constructor' => static fn(): MessagePublisherInterface => ChatScriptMessengerFactory::createPublisher(
            ServiceLocator::getInstance(),
        ),
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
    StartTradeChatScriptUseCase::class => [
        'className' => StartTradeChatScriptUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(AdvertisementRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptExecutionRepository::class),
            ServiceLocator::getInstance()->get('exchange.chat_script_step.publisher'),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    ExecuteQueuedChatScriptStepUseCase::class => [
        'className' => ExecuteQueuedChatScriptStepUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ChatScriptExecutionRepository::class),
            ServiceLocator::getInstance()->get(ChatScriptStepRepository::class),
            ServiceLocator::getInstance()->get(TradeRepository::class),
            ServiceLocator::getInstance()->get(TradeMessageRepository::class),
            ServiceLocator::getInstance()->get(BybitChatGatewayInterface::class),
            ServiceLocator::getInstance()->get('exchange.chat_script_step.publisher'),
            Log::getLogger(LogChannelEnum::exchange),
        ],
    ],
    ExecuteChatScriptsCommand::class => [
        'className' => ExecuteChatScriptsCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ProcessPendingChatScriptsUseCase::class),
        ],
    ],
    ConsumeChatScriptStepsUseCase::class => [
        'className' => ConsumeChatScriptStepsUseCase::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumerRunnerInterface::class),
            ServiceLocator::getInstance()->get(AmqpConnectionFactory::class),
            ChatScriptMessengerFactory::createBus(ServiceLocator::getInstance()),
        ],
    ],
    ChatScriptStepConsumerCommand::class => [
        'className' => ChatScriptStepConsumerCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(ConsumeChatScriptStepsUseCase::class),
        ],
    ],
    TestChatScriptStepCommand::class => [
        'className' => TestChatScriptStepCommand::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get('exchange.chat_script_step.publisher'),
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
