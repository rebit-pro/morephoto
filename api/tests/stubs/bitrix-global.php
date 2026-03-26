<?php

declare(strict_types=1);

namespace {
    if (!class_exists(CEvent::class)) {
        class CEvent
        {
            /** @var array{eventName: string, siteId: string|array, fields: array<string, mixed>}|null */
            public static ?array $lastSendImmediateCall = null;
            public static int|false $sendImmediateResult = 1;

            /**
             * @param array<string, mixed> $fields
             */
            public static function SendImmediate(string $eventName, string|array $siteId, array $fields): int|false
            {
                self::$lastSendImmediateCall = [
                    'eventName' => $eventName,
                    'siteId' => $siteId,
                    'fields' => $fields,
                ];

                return self::$sendImmediateResult;
            }
        }
    }

    if (!class_exists(CUser::class)) {
        class CUser
        {
            public string $LAST_ERROR = '';

            /**
             * @param array<string, mixed> $fields
             */
            public function Add(array $fields): int|false
            {
                return 1;
            }

            /**
             * @param array<string, mixed> $fields
             */
            public function Update(int $id, array $fields): bool
            {
                return true;
            }
        }
    }
}
