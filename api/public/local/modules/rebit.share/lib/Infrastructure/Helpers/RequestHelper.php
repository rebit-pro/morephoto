<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Helpers;

use Bitrix\Main\HttpRequest;

final class RequestHelper
{
    /**
     * Собирает входящие поля со всех типов запросов
     *
     * @return array<string, mixed>
     */
    public static function collectRequestValues(HttpRequest $request): array
    {
        return array_merge(
            $request->getValues(),
            $request->getJsonList()->getValues(),
            $request->getPostList()->getValues(),
        );
    }

    public static function getSiteUrl(): string
    {
        return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    }

    public static function getFullUrl(string $pageUri = '/'): string
    {
        $prefix = str_contains($pageUri, 'orteka.ru') ? '' : self::getSiteUrl();

        return $prefix . $pageUri;
    }
}
