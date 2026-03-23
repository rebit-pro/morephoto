<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Bybit;

use Rebit\Share\Shared\Exception\HttpException;

/**
 * Исключение при ошибках взаимодействия с Bybit API.
 */
final class BybitApiException extends HttpException
{
    public const int HTTP_DEFAULT_EXCEPTION_CODE = 502;
    public const string DEFAULT_ERROR_MESSAGE = 'Bybit API Error';

    public function __construct(
        string $message = self::DEFAULT_ERROR_MESSAGE,
        int $code = self::HTTP_DEFAULT_EXCEPTION_CODE,
        private readonly int $bybitRetCode = 0,
        ?\Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getBybitRetCode(): int
    {
        return $this->bybitRetCode;
    }
}
