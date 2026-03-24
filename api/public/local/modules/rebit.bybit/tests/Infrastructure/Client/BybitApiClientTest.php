<?php

declare(strict_types=1);

namespace Rebit\Bybit\Tests\Infrastructure\Client;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Bybit\Infrastructure\Auth\HmacSignatureGenerator;
use Rebit\Bybit\Infrastructure\Client\BybitApiClient;
use Rebit\Share\Application\Contract\Bybit\BybitApiException;
use Rebit\Share\Application\Contract\Bybit\BybitCredentials;
use Rebit\Share\Application\Contract\Bybit\BybitEnvironmentEnum;
use Rebit\Share\Application\Contract\Bybit\BybitResponseDto;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClient;

/**
 * @internal
 */
final class BybitApiClientTest extends TestCase
{
    private function createClient(RebitHttpClient $httpClient): BybitApiClient
    {
        return new BybitApiClient(
            httpClient: $httpClient,
            signatureGenerator: new HmacSignatureGenerator(),
            logger: new NullLogger(),
        );
    }

    public function testGetReturnsResponseDtoOnSuccess(): void
    {
        $httpClient = $this->createMock(RebitHttpClient::class);

        $httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'retCode' => 0,
                'retMsg' => 'OK',
                'result' => ['apiKey' => 'xxx'],
                'retExtInfo' => [],
                'time' => 1700000000000,
            ])
        ;

        $credentials = new BybitCredentials('api-key', 'api-secret');
        $result = $this->createClient($httpClient)
            ->get('/v5/user/query-api', $credentials, BybitEnvironmentEnum::Testnet)
        ;

        self::assertInstanceOf(BybitResponseDto::class, $result);
        self::assertSame(0, $result->retCode);
        self::assertSame('OK', $result->retMsg);
    }

    public function testGetThrowsOnNonZeroRetCode(): void
    {
        $httpClient = $this->createStub(RebitHttpClient::class);

        $httpClient
            ->method('get')
            ->willReturn([
                'retCode' => 10003,
                'retMsg' => 'Invalid API key',
                'result' => [],
                'retExtInfo' => [],
                'time' => 0,
            ])
        ;

        $credentials = new BybitCredentials('bad-key', 'bad-secret');

        $this->expectException(BybitApiException::class);
        $this->expectExceptionMessage('Bybit API error [10003]: Invalid API key');

        $this->createClient($httpClient)
            ->get('/v5/user/query-api', $credentials, BybitEnvironmentEnum::Testnet)
        ;
    }

    public function testGetThrowsOnHttpException(): void
    {
        $httpClient = $this->createStub(RebitHttpClient::class);

        $httpClient
            ->method('get')
            ->willThrowException(new \Exception('Connection refused'))
        ;

        $credentials = new BybitCredentials('key', 'secret');

        $this->expectException(BybitApiException::class);
        $this->expectExceptionMessage('Bybit API request failed: Connection refused');

        $this->createClient($httpClient)
            ->get('/v5/test', $credentials, BybitEnvironmentEnum::Testnet)
        ;
    }

    public function testPostReturnsResponseDtoOnSuccess(): void
    {
        $httpClient = $this->createMock(RebitHttpClient::class);

        $httpClient
            ->expects($this->once())
            ->method('post')
            ->willReturn([
                'retCode' => 0,
                'retMsg' => 'OK',
                'result' => ['orderId' => '123'],
                'retExtInfo' => [],
                'time' => 1700000000000,
            ])
        ;

        $credentials = new BybitCredentials('api-key', 'api-secret');
        $result = $this->createClient($httpClient)->post(
            '/v5/order/create',
            $credentials,
            BybitEnvironmentEnum::Mainnet,
            ['symbol' => 'BTCUSDT'],
        );

        self::assertSame(0, $result->retCode);
        self::assertSame('123', $result->result['orderId']);
    }

    public function testPostThrowsOnNonZeroRetCode(): void
    {
        $httpClient = $this->createStub(RebitHttpClient::class);

        $httpClient
            ->method('post')
            ->willReturn([
                'retCode' => 110001,
                'retMsg' => 'Insufficient balance',
                'result' => [],
                'retExtInfo' => [],
                'time' => 0,
            ])
        ;

        $credentials = new BybitCredentials('key', 'secret');

        $this->expectException(BybitApiException::class);

        $this->createClient($httpClient)
            ->post('/v5/order/create', $credentials, BybitEnvironmentEnum::Mainnet)
        ;
    }

    public function testGetWithQueryParams(): void
    {
        $capturedUrl = '';

        $httpClient = $this->createMock(RebitHttpClient::class);

        $httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function(string $url) use (&$capturedUrl): array {
                $capturedUrl = $url;

                return [
                    'retCode' => 0,
                    'retMsg' => 'OK',
                    'result' => [],
                    'retExtInfo' => [],
                    'time' => 0,
                ];
            })
        ;

        $credentials = new BybitCredentials('key', 'secret');
        $this->createClient($httpClient)->get(
            '/v5/position/list',
            $credentials,
            BybitEnvironmentEnum::Testnet,
            ['category' => 'linear', 'symbol' => 'BTCUSDT'],
        );

        self::assertStringContainsString('category=linear', $capturedUrl);
        self::assertStringContainsString('symbol=BTCUSDT', $capturedUrl);
    }

    public function testBybitApiExceptionContainsRetCode(): void
    {
        $httpClient = $this->createStub(RebitHttpClient::class);

        $httpClient
            ->method('get')
            ->willReturn([
                'retCode' => 33004,
                'retMsg' => 'API key expired',
                'result' => [],
                'retExtInfo' => [],
                'time' => 0,
            ])
        ;

        $credentials = new BybitCredentials('key', 'secret');

        try {
            $this->createClient($httpClient)
                ->get('/v5/user/query-api', $credentials, BybitEnvironmentEnum::Testnet)
            ;
            self::fail('Expected BybitApiException');
        } catch (BybitApiException $e) {
            self::assertSame(33004, $e->getBybitRetCode());
        }
    }

    public function testNormalizesOldP2PFormatOnSuccess(): void
    {
        $httpClient = $this->createMock(RebitHttpClient::class);
        $httpClient
            ->expects($this->once())
            ->method('post')
            ->willReturn([
                'ret_code' => 0,
                'ret_msg' => 'OK',
                'result' => ['items' => []],
                'ext_info' => [],
                'time_now' => '1677131201.177999',
            ])
        ;
        $credentials = new BybitCredentials('api-key', 'api-secret');
        $result = $this->createClient($httpClient)->post(
            '/v5/p2p/item/online',
            $credentials,
            BybitEnvironmentEnum::Mainnet,
            ['tokenId' => 'USDT', 'currencyId' => 'RUB'],
        );
        self::assertSame(0, $result->retCode);
        self::assertSame('OK', $result->retMsg);
    }
    public function testNormalizesOldP2PFormatOnError(): void
    {
        $httpClient = $this->createStub(RebitHttpClient::class);
        $httpClient
            ->method('post')
            ->willReturn([
                'ret_code' => 10004,
                'ret_msg' => 'error sign!',
                'result' => [],
                'ext_info' => [],
                'time_now' => '1677131201.177999',
            ])
        ;
        $credentials = new BybitCredentials('key', 'secret');
        $this->expectException(BybitApiException::class);
        $this->expectExceptionMessage('Bybit API error [10004]: error sign!');
        $this->createClient($httpClient)
            ->post('/v5/p2p/order/pending/simplifyList', $credentials, BybitEnvironmentEnum::Mainnet)
        ;
    }
}
