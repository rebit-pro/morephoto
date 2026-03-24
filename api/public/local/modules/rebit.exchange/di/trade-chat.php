<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Application\TradeChat\UseCase\ExecuteChatScriptUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\GetChatHistoryUseCase;
use Rebit\Exchange\Application\TradeChat\UseCase\SendMessageUseCase;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Exchange\Infrastructure\Bybit\BybitChatGateway;
use Rebit\Exchange\Presentation\Controller\TradeChatController;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;

return [
    TradeMessageRepository::class => [
        'className' => TradeMessageRepository::class,
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
    GetChatHistoryUseCase::class => [
        'constructor' => static function(): GetChatHistoryUseCase {
            $sl = ServiceLocator::getInstance();

            return new GetChatHistoryUseCase(
                $sl->get(TradeMessageRepository::class),
                $sl->get(TradeRepository::class),
            );
        },
    ],
    SendMessageUseCase::class => [
        'constructor' => static function(): SendMessageUseCase {
            $sl = ServiceLocator::getInstance();

            return new SendMessageUseCase(
                $sl->get(TradeMessageRepository::class),
                $sl->get(TradeRepository::class),
                $sl->get(BybitChatGatewayInterface::class),
            );
        },
    ],
    ExecuteChatScriptUseCase::class => [
        'constructor' => static function(): ExecuteChatScriptUseCase {
            $sl = ServiceLocator::getInstance();

            return new ExecuteChatScriptUseCase(
                $sl->get(ChatScriptRepository::class),
                $sl->get(ChatScriptStepRepository::class),
                $sl->get(TradeRepository::class),
                $sl->get(TradeMessageRepository::class),
                $sl->get(BybitChatGatewayInterface::class),
                Log::getLogger(LogChannelEnum::exchange),
            );
        },
    ],
    TradeChatController::class => [
        'constructor' => static function(): TradeChatController {
            $sl = ServiceLocator::getInstance();

            return new TradeChatController(
                $sl->get(GetChatHistoryUseCase::class),
                $sl->get(SendMessageUseCase::class),
            );
        },
    ],
];
