<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Factory;

use Bitrix\Main\ArgumentException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Infrastructure\Helpers\ValidationHelper;
use Rebit\Share\Shared\Interface\DtoInterface;

/**
 * @template T of DtoInterface
 */
final class DtoFactory
{
    /**
     * @param class-string<T>      $dtoClass
     * @param array<string, mixed> $data
     *
     * @throws ArgumentException
     * @throws \ReflectionException
     * @throws ValidationHttpException
     */
    public static function create(string $dtoClass, array $data): DtoInterface
    {
        if (!class_exists($dtoClass)) {
            throw new ArgumentException("Класс {$dtoClass} не существует");
        }

        $reflection = new \ReflectionClass($dtoClass);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            throw new ArgumentException("У класса {$dtoClass} нет конструктора");
        }

        $parameters = $constructor->getParameters();
        $arguments = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            $paramValue = $data[$paramName] ?? null;

            if ($parameter->isDefaultValueAvailable() && null === $paramValue) {
                $paramValue = $parameter->getDefaultValue();
            } elseif (!$parameter->allowsNull() && null === $paramValue) {
                throw new ArgumentException("Обязательный параметр {$paramName} не передан");
            }

            $arguments[] = $paramValue;
        }

        /** @var T $dto */
        $dto = $reflection->newInstanceArgs($arguments);

        ValidationHelper::validate($dto);

        return $dto;
    }
}
