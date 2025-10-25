<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Controller;

use Rebit\Share\Infrastructure\Controller\Responses\JsonResponse;
use Rebit\Share\Infrastructure\Controller\Serializers\CommonSerializer;
use Rebit\Share\Infrastructure\Interface\SerializerInterface;
use Rebit\Share\Shared\Interface\ResultDtoInterface;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;

/**
 * Основной класс для наследования Json-контроллеров.
 */
abstract class BaseJsonController extends AbstractJsonController
{
    protected function getResponse(
        array|ResultDtoInterface $data,
        array $meta = [],
        ?SerializerInterface $serializer = null,
    ): ControllerJson {
        $serializer = $serializer ?? CommonSerializer::createDefault();

        return (new JsonResponse($serializer, $data, $meta))->getResponse();
    }
}
