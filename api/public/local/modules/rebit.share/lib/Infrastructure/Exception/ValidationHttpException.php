<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Exception;

use Rebit\Share\Shared\Exception\HttpException;

/**
 * Исключение при ошибках валидации данных в запросах
 */
final class ValidationHttpException extends HttpException
{
    public const int HTTP_DEFAULT_EXCEPTION_CODE = 400;
    public const string DEFAULT_ERROR_MESSAGE = 'Ошибка валидации данных.';
}
