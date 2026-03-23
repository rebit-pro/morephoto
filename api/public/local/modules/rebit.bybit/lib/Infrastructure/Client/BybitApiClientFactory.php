<?php

declare(strict_types=1);

namespace Rebit\Bybit\Infrastructure\Client;

use Psr\Log\LoggerInterface;
use Rebit\Bybit\Infrastructure\Auth\HmacSignatureGenerator;
use Rebit\Share\Application\Contract\Bybit\BybitClientInterface;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClientFactory;

final class BybitApiClientFactory
{
    public static function create(LoggerInterface $logger): BybitClientInterface
    {
        return new BybitApiClient(
            httpClient: RebitHttpClientFactory::create($logger),
            signatureGenerator: new HmacSignatureGenerator(),
            logger: $logger,
        );
    }
}
