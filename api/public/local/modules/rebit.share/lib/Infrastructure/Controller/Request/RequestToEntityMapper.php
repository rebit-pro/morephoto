<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Controller\Request;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use Rebit\Share\Infrastructure\Exception\RequestParameterException;
use Rebit\Share\Infrastructure\Helpers\RequestHelper;
use Rebit\Share\Infrastructure\Interface\RequestMapperInterface;

/**
 * Маппит переданный в запросе ID на сущность указанную в Action
 */
final readonly class RequestToEntityMapper implements RequestMapperInterface
{
    public function __construct(
        private HttpRequest $request,
    ) {
    }

    public function supports(string $className): bool
    {
        return is_subclass_of($className, EntityObject::class);
    }

    /**
     * @param class-string $className
     *
     * @throws ArgumentException
     * @throws RequestParameterException
     * @throws SystemException
     */
    public function map(string $className): EntityObject
    {
        $values = RequestHelper::collectRequestValues($this->request);
        if (1 !== count($values)) {
            throw new RequestParameterException('Для маппинга сущности нужно указывать только один параметр!');
        }

        $id = (int)array_values($values)[0];

        return $className::$dataClass::getById($id)->fetchObject();
    }
}
