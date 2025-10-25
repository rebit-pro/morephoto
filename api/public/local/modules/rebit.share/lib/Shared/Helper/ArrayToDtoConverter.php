<?php

declare(strict_types=1);

namespace Rebit\Share\Shared\Helper;

use Bitrix\Main\Engine\Response\Converter;
use Rebit\Share\Shared\Interface\DtoInterface;

/**
 * @template T
 */
final class ArrayToDtoConverter
{
    /**
     * Мапит массив из массивов, возвращает массив из DTO
     *
     * @param class-string<T> $dtoClass
     *
     * @return T[]
     *
     * @throws \InvalidArgumentException
     */
    public static function mapCollection(array $data, string $dtoClass): array
    {
        return array_map(static fn(array $item) => self::map($item, $dtoClass), $data);
    }

    /**
     * Конвертирует массив в DTO.
     *
     * Обязательное условие: названия ключей массива и их последовательность должны точно соответствовать
     * свойствам DTO в конструкторе, либо названия могут быть аналогичными в snake_case или верхнем регистре.
     * Пример соответствия при маппиге: ACTIVE = active, TIME_CREATED = timeCreated, oneTime = oneTime, Active = Active.
     *
     * Если последовательность полей в DTO отличается от последовательности ключей в массиве, то будет выброшено
     * исключение.
     * Если массив содержит поля, которых нет в DTO, то будет выброшено исключение.
     * Если в массиве не стринговые ключи, то будет выброшено исключение.
     *
     * @param class-string<T> $dtoClass
     *
     * @return T
     *
     * @throws \InvalidArgumentException
     */
    public static function map(array $data, string $dtoClass): object
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Array is empty.');
        }

        if (!class_exists($dtoClass)) {
            throw new \InvalidArgumentException('Class not found: ' . $dtoClass);
        }

        $data = self::convertArrayKeysToCamelCase($data);

        $params = self::getConstructorParams($dtoClass);
        self::arrayKeyValidation($params, $data);
        // Если в параметрах конструктора есть DTO, то замаппим их
        $data = self::recursiveDtoMapping($params, $data);

        return new $dtoClass(...$data);
    }

    /**
     * Преобразование ключей массива в camelCase и нижний регистр.
     * Если в ключе уже есть 2 регистра, а так же нет _, то такой ключ игнорируется.
     */
    private static function convertArrayKeysToCamelCase(array $array): array
    {
        $converter = new Converter(Converter::LC_FIRST | Converter::TO_CAMEL | Converter::KEYS);

        return array_combine(
            array_map(static function ($key) use ($converter) {
                // если строка в верхнем регистре или есть _, то мы ее конвертируем
                if ($key === mb_strtoupper($key) || str_contains($key, '_')) {
                    return $converter->process($key);
                }

                return $key;
            }, array_keys($array)),
            $array,
        );
    }

    /**
     * @param class-string<T> $dtoClass
     *
     * @return \ReflectionParameter[]
     *
     * @throws \InvalidArgumentException
     */
    private static function getConstructorParams(string $dtoClass): array
    {
        // Исключения не будет, тк наличие класса проверяется выше.
        $reflectionClass = new \ReflectionClass($dtoClass);
        $constructorParams = $reflectionClass->getConstructor()?->getParameters();
        if (empty($constructorParams)) {
            throw new \InvalidArgumentException('Constructor\'s params not found: ' . $dtoClass);
        }

        return $constructorParams;
    }

    /**
     * Проверят соответствие ключей в массиве для мапинга и конструкторе DTO
     *
     * @param \ReflectionParameter[] $constructorParams
     *
     * @throws \InvalidArgumentException
     */
    private static function arrayKeyValidation(array $constructorParams, array $data): void
    {
        $dtoFields = array_map(static fn($param) => $param->getName(), $constructorParams);
        $dataKeys = array_keys($data);

        $missingKeys = array_diff($dtoFields, $dataKeys);
        $extraKeys = array_diff($dataKeys, $dtoFields);

        if (!empty($missingKeys) || !empty($extraKeys)) {
            $message = 'Array keys do not match DTO fields.';
            if (!empty($missingKeys)) {
                $message .= ' Missing keys: ' . implode(', ', $missingKeys);
            }
            if (!empty($extraKeys)) {
                $message .= ' Extra keys: ' . implode(', ', $extraKeys);
            }
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * Если в параметрах конструктора есть DTO, то находим и маппим их рекурсивно.
     *
     * @param \ReflectionParameter[] $constructorParams
     */
    private static function recursiveDtoMapping(array $constructorParams, array $data): array
    {
        foreach ($constructorParams as $param) {
            $paramType = $param->getType()?->getName();
            if (null === $paramType) {
                continue;
            }

            if (!class_exists($paramType)) {
                continue;
            }

            $implements = class_implements($paramType);
            if (!is_array($implements)) {
                continue;
            }

            if (in_array(DtoInterface::class, $implements, true)) {
                $data[$param->getName()] = self::map($data[$param->getName()], $paramType);
            }
        }

        return $data;
    }
}
