<?php

declare(strict_types=1);

namespace Rebit\Exchange\Infrastructure\Bybit;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Currency\Port\BybitSpotTickerGatewayInterface;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClient;

/**
 * Адаптер для получения спотовых цен с публичного Bybit API.
 *
 * GET /v5/market/tickers?category=spot&symbol={symbol}
 * Авторизация не требуется.
 */
final readonly class BybitSpotTickerGateway implements BybitSpotTickerGatewayInterface
{
    private const string ENDPOINT = '/v5/market/tickers';

    public function __construct(
        private RebitHttpClient $httpClient,
        private string $baseUrl,
        private LoggerInterface $logger,
    ) {}

    public function getLastPrice(string $symbol): ?float
    {
        try {
            $url = $this->baseUrl . self::ENDPOINT . '?' . http_build_query([
                'category' => 'spot',
                'symbol' => mb_strtoupper($symbol),
            ]);

            $response = $this->httpClient->get($url);

            $retCode = (int)($response['retCode'] ?? -1);
            if (0 !== $retCode) {
                $this->logger->warning('Bybit spot ticker: non-zero retCode', [
                    'symbol' => $symbol,
                    'retCode' => $retCode,
                    'retMsg' => $response['retMsg'] ?? '',
                ]);

                return null;
            }

            /** @var list<array<string, mixed>> $list */
            $list = $response['result']['list'] ?? [];
            if ([] === $list || !isset($list[0]['lastPrice'])) {
                return null;
            }

            $price = (float)$list[0]['lastPrice'];

            return 0.0 < $price ? $price : null;
        } catch (\Throwable $e) {
            $this->logger->warning('Bybit spot ticker: request failed', [
                'symbol' => $symbol,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
