<?php

declare(strict_types=1);

namespace Rebit\Notification\Infrastructure\Lead;

use Bitrix\Main\ArgumentException;
use Psr\Log\LoggerInterface;
use Rebit\Notification\Application\Lead\Dto\LeadMessageDto;
use Rebit\Notification\Application\Lead\Port\LeadNotifierInterface;
use Rebit\Share\Infrastructure\HttpClient\Exception\HttpClientException;
use Rebit\Share\Infrastructure\HttpClient\RebitHttpClient;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Доставка заявки в Telegram через Bot API (sendMessage).
 *
 * Токен бота и chat_id хранятся в окружении сервера, не в коде сайта.
 */
final readonly class TelegramLeadNotifier implements LeadNotifierInterface
{
    public function __construct(
        private RebitHttpClient $httpClient,
        private LoggerInterface $logger,
        private string $botToken,
        private string $chatId,
        private string $apiBaseUrl,
    ) {}

    /**
     * @throws HttpException
     */
    public function notify(LeadMessageDto $lead): void
    {
        if ('' === $this->botToken || '' === $this->chatId) {
            $this->logger->error('Telegram-получатель заявок не настроен: пустой токен или chat_id');

            throw new HttpException('Сервис заявок временно недоступен', 503);
        }

        try {
            $response = $this->httpClient->post(
                $this->buildSendMessageUrl(),
                [
                    'chat_id' => $this->chatId,
                    'text' => $this->buildText($lead),
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => 'true',
                ],
            );
        } catch (ArgumentException|HttpClientException $exception) {
            $this->logger->error('Не удалось отправить заявку в Telegram', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            throw new HttpException('Не удалось отправить заявку', 502, $exception);
        }

        if (true !== ($response['ok'] ?? false)) {
            $this->logger->error('Telegram отклонил отправку заявки', [
                'response' => $response,
            ]);

            throw new HttpException('Не удалось отправить заявку', 502);
        }
    }

    private function buildSendMessageUrl(): string
    {
        return sprintf('%s/bot%s/sendMessage', rtrim($this->apiBaseUrl, '/'), $this->botToken);
    }

    private function buildText(LeadMessageDto $lead): string
    {
        $lines = [
            '🆕 <b>Новая заявка с сайта</b>',
            '',
            '👤 <b>Имя:</b> ' . $this->escape($lead->name),
            '📞 <b>Телефон:</b> ' . $this->escape($lead->phone),
            '',
            '📝 <b>Описание:</b>',
            $this->escape($lead->description),
        ];

        if ('' !== $lead->page) {
            $lines[] = '';
            $lines[] = '🔗 ' . $this->escape($lead->page);
        }

        return implode("\n", $lines);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
