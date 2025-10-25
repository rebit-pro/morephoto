<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Controller\Filters;

use Bitrix\Main\Engine\Action;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\HttpResponse;
use Rebit\Share\Infrastructure\Controller\AbstractController;
use Rebit\Share\Shared\Enum\LogChannelEnum;
use Rebit\Share\Shared\Facade\Log;
use Rebit\Share\Infrastructure\Helpers\RequestHelper;
use Bitrix\Main\Engine\Response\Json;

/**
 * Логирует входящий запрос и его результаты.
 */
final class LoggerFilter extends Base
{
    public function __construct(private readonly LogChannelEnum $channel = LogChannelEnum::default)
    {
        parent::__construct();
    }

    /**
     * Логируем входящий запрос
     *
     * @param Event{
     *     moduleId: string,
     *     type: string,
     *     parameters: array{
     *         action: Action,
     *         controller: AbstractController,
     *     }
     * } $event
     */
    public function onBeforeAction(Event $event): ?EventResult
    {
        $data = $this->extractRequestData($event->getParameter('controller'));
        Log::channel($this->channel)->info('REQUEST', $data);

        return null;
    }

    /**
     * Логируем результат запроса
     *
     * @param Event{
     *     moduleId: string,
     *     type: string,
     *     parameters: array{
     *         action: Action,
     *         controller: AbstractController,
     *         result: HttpResponse,
     *     }
     * } $event
     *
     * @throws \JsonException
     */
    public function onAfterAction(Event $event): ?EventResult
    {
        /** @var AbstractController $controller */
        $controller = $event->getParameter('controller');
        /** @var HttpResponse $response */
        $response = $event->getParameter('result');
        $requestData = $this->extractRequestData($controller);
        $responseData = $this->extractResponseData($response);

        $payload = [
            'request' => $requestData,
            'response' => $responseData,
        ];

        Log::channel($this->channel)->info('RESPONSE', $payload);

        return null;
    }

    /**
     * @return array{
     *     method: string,
     *     uri: string,
     *     payload: array<string, mixed>,
     *     requestId: string,
     * }
     */
    private function extractRequestData(AbstractController $controller): array
    {
        $request = $controller->getRequest();

        return [
            'request' => RequestHelper::collectRequestValues($request),
        ];
    }

    /**
     * Если ответ json, то возвращаем декодированный массив, иначе массив с текстом ответа.
     *
     * @throws \JsonException
     */
    private function extractResponseData(HttpResponse $response): array
    {
        $content = $response->getContent();
        if (!$response instanceof Json) {
            return [$content];
        }

        return json_decode($content, true, flags: JSON_THROW_ON_ERROR) ?? [];
    }
}
