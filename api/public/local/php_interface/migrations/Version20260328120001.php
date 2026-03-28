<?php

declare(strict_types=1);

namespace Sprint\Migration;

use Sprint\Migration\Exceptions\HelperException;

/**
 * Создаёт тип почтового события REBIT_NOTIFICATION_TRADE_DISCOVERED
 * и HTML-шаблон письма для уведомления о новой P2P-сделке.
 */
class Version20260328120001 extends Version
{
    protected $author = 'auto';

    protected $description = 'Почтовое событие REBIT_NOTIFICATION_TRADE_DISCOVERED';

    private const string EVENT_NAME = 'REBIT_NOTIFICATION_TRADE_DISCOVERED';
    private const string EVENT_SUBJECT = 'Rebit P2P — новая сделка обнаружена';
    private const string SITE_ID = 's1';

    /**
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();

        $helper->Event()->saveEventType(self::EVENT_NAME, [
            'LID' => 'ru',
            'NAME' => 'Rebit P2P — новая сделка обнаружена',
            'DESCRIPTION' => implode("\n", [
                '#EMAIL_TO# - E-mail получателя',
                '#TRADE_ID# - ID сделки',
                '#SIDE# - Направление сделки (buy/sell)',
                '#FIAT_AMOUNT# - Сумма в фиатной валюте',
                '#COUNTERPARTY_NAME# - Имя контрагента',
            ]),
        ]);

        $helper->Event()->saveEventMessage(self::EVENT_NAME, [
            'ACTIVE' => 'Y',
            'LID' => self::SITE_ID,
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL_TO#',
            'SUBJECT' => self::EVENT_SUBJECT,
            'BODY_TYPE' => 'html',
            'MESSAGE' => $this->getHtmlBody(),
        ]);
    }

    /**
     * @throws HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();

        $helper->Event()->deleteEventMessage([
            'EVENT_NAME' => self::EVENT_NAME,
            'SUBJECT' => self::EVENT_SUBJECT,
        ]);

        $helper->Event()->deleteEventType([
            'EVENT_NAME' => self::EVENT_NAME,
            'LID' => 'ru',
        ]);
    }

    private function getHtmlBody(): string
    {
        return <<<'HTML'
<div style="margin:0;padding:32px 16px;background-color:#f4f7fb;font-family:Arial,sans-serif;color:#17233b;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 30px rgba(18,38,63,0.08);">
        <tr>
            <td style="padding:32px 40px;background:linear-gradient(135deg,#0f172a 0%,#1d4ed8 100%);color:#ffffff;">
                <div style="font-size:12px;line-height:18px;letter-spacing:1.6px;text-transform:uppercase;opacity:0.8;">Rebit P2P</div>
                <div style="margin-top:8px;font-size:28px;line-height:36px;font-weight:700;">Новая сделка обнаружена</div>
                <div style="margin-top:8px;font-size:15px;line-height:24px;opacity:0.9;">В вашем аккаунте появилась новая P2P-сделка.</div>
            </td>
        </tr>
        <tr>
            <td style="padding:40px;">
                <p style="margin:0 0 16px;font-size:16px;line-height:24px;">Здравствуйте!</p>
                <p style="margin:0 0 24px;font-size:16px;line-height:24px;">
                    Обнаружена новая P2P-сделка в <strong>Rebit P2P</strong>.
                </p>
                <table style="width:100%;margin:0 0 24px;border-collapse:collapse;background:#f8fafc;border:1px solid #dbe6f3;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="padding:12px 16px;font-size:14px;color:#64748b;border-bottom:1px solid #dbe6f3;">ID сделки</td>
                        <td style="padding:12px 16px;font-size:14px;font-weight:700;color:#0f172a;border-bottom:1px solid #dbe6f3;">#TRADE_ID#</td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px;font-size:14px;color:#64748b;border-bottom:1px solid #dbe6f3;">Направление</td>
                        <td style="padding:12px 16px;font-size:14px;font-weight:700;color:#0f172a;border-bottom:1px solid #dbe6f3;">#SIDE#</td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px;font-size:14px;color:#64748b;border-bottom:1px solid #dbe6f3;">Сумма (фиат)</td>
                        <td style="padding:12px 16px;font-size:14px;font-weight:700;color:#0f172a;border-bottom:1px solid #dbe6f3;">#FIAT_AMOUNT#</td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px;font-size:14px;color:#64748b;">Контрагент</td>
                        <td style="padding:12px 16px;font-size:14px;font-weight:700;color:#0f172a;">#COUNTERPARTY_NAME#</td>
                    </tr>
                </table>
                <p style="margin:0;font-size:16px;line-height:24px;">
                    Перейдите в панель управления для просмотра деталей.
                </p>
            </td>
        </tr>
        <tr>
            <td style="padding:24px 40px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;line-height:20px;color:#64748b;">
                Это автоматическое письмо. Пожалуйста, не отвечайте на него.
            </td>
        </tr>
    </table>
</div>
HTML;
    }
}
