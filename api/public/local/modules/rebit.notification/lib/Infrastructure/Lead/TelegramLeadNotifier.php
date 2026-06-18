<?php

declare(strict_types=1);

namespace Rebit\Notification\Infrastructure\Lead;

use Psr\Log\LoggerInterface;
use Rebit\Notification\Application\Lead\Dto\LeadMessageDto;
use Rebit\Notification\Application\Lead\Port\LeadNotifierInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Доставка заявки в Telegram через Bot API (sendMessage).
 *
 * Используем cURL напрямую (а не RebitHttpClient/Bitrix HttpClient), потому что
 * с сервера api.telegram.org заблокирован и запрос идёт через прокси, в т.ч.
 * SOCKS5 — это умеет cURL, но не Bitrix HttpClient. Тип прокси определяется
 * по схеме в $proxy (например socks5h://user:pass@host:port или http://...).
 *
 * Токен бота, chat_id и прокси берутся из окружения сервера.
 */
final readonly class TelegramLeadNotifier implements LeadNotifierInterface
{
    private const int TIMEOUT_SECONDS = 12;

    public function __construct(
        private LoggerInterface $logger,
        private string $botToken,
        private string $chatId,
        private string $apiBaseUrl,
        private string $proxy,
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

        $handle = curl_init($this->buildSendMessageUrl());

        if (false === $handle) {
            throw new HttpException('Не удалось инициализировать запрос в Telegram', 500);
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => self::TIMEOUT_SECONDS,
            CURLOPT_CONNECTTIMEOUT => self::TIMEOUT_SECONDS,
            CURLOPT_POSTFIELDS => http_build_query([
                'chat_id' => $this->chatId,
                'text' => $this->buildText($lead),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => 'true',
            ]),
        ];

        if ('' !== $this->proxy) {
            $options[CURLOPT_PROXY] = $this->proxy;
        }

        curl_setopt_array($handle, $options);

        $response = curl_exec($handle);
        $status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if (!is_string($response) || 200 !== $status) {
            $this->logger->error('Не удалось отправить заявку в Telegram', [
                'status' => $status,
                'error' => $error,
                'response' => is_string($response) ? mb_substr($response, 0, 500) : '',
            ]);

            throw new HttpException('Не удалось отправить заявку', 502);
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded) || true !== ($decoded['ok'] ?? false)) {
            $this->logger->error('Telegram отклонил отправку заявки', [
                'response' => mb_substr($response, 0, 500),
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
