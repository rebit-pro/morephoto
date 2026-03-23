<?php

declare(strict_types=1);

/**
 * Рабочие стабы Bitrix-классов для PHPUnit-тестов.
 * Подключаются в tests/bootstrap.php.
 */

namespace Bitrix\Main\Type;

if (!class_exists(Date::class)) {
    class Date
    {
        protected int $timestamp;

        public function __construct(string $date = '', string $format = '')
        {
            $this->timestamp = ('' !== $date) ? (int)strtotime($date) : time();
        }

        public function format(string $format): string
        {
            return date($format, $this->timestamp);
        }

        public function getTimestamp(): int
        {
            return $this->timestamp;
        }
    }
}

if (!class_exists(DateTime::class)) {
    class DateTime extends Date
    {
        public static function createFromTimestamp(int $timestamp): static
        {
            $instance = new static();
            $instance->timestamp = $timestamp;

            return $instance;
        }

        public function toString(): string
        {
            return date('d.m.Y H:i:s', $this->timestamp);
        }
    }
}

namespace Bitrix\Main\Web;

if (!class_exists(Json::class)) {
    class Json
    {
        public static function encode(mixed $data): string
        {
            return (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        public static function decode(string $data): mixed
        {
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }
    }
}
