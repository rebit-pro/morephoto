# Шаблон письма для админки Битрикса: новая сделка обнаружена
Шаблон для почтового события, отправляемого модулем `rebit.notification`
при обнаружении новой P2P-сделки.
## Код почтового события
```text
REBIT_NOTIFICATION_TRADE_DISCOVERED
```
## Макросы
```text
#EMAIL_TO#
#TRADE_ID#
#SIDE#
#FIAT_AMOUNT#
#COUNTERPARTY_NAME#
```
## Тема письма
```text
Rebit P2P — новая сделка обнаружена
```
## HTML-версия письма
```html
<p>Здравствуйте!</p>
<p>
    Обнаружена новая P2P-сделка в <strong>Rebit P2P</strong>.
</p>
<table style="border-collapse: collapse; margin: 16px 0;">
    <tr>
        <td style="padding: 4px 12px 4px 0; font-weight: bold;">ID сделки:</td>
        <td>#TRADE_ID#</td>
    </tr>
    <tr>
        <td style="padding: 4px 12px 4px 0; font-weight: bold;">Направление:</td>
        <td>#SIDE#</td>
    </tr>
    <tr>
        <td style="padding: 4px 12px 4px 0; font-weight: bold;">Сумма (фиат):</td>
        <td>#FIAT_AMOUNT#</td>
    </tr>
    <tr>
        <td style="padding: 4px 12px 4px 0; font-weight: bold;">Контрагент:</td>
        <td>#COUNTERPARTY_NAME#</td>
    </tr>
</table>
<p>
    Перейдите в панель управления для просмотра деталей.
</p>
<hr>
<p style="color: #777; font-size: 12px;">
    Это автоматическое письмо, пожалуйста, не отвечайте на него.<br>
    Получатель: #EMAIL_TO#
</p>
```
## Text-версия письма
```text
Здравствуйте!
Обнаружена новая P2P-сделка в Rebit P2P.
ID сделки: #TRADE_ID#
Направление: #SIDE#
Сумма (фиат): #FIAT_AMOUNT#
Контрагент: #COUNTERPARTY_NAME#
Перейдите в панель управления для просмотра деталей.
Это автоматическое письмо, пожалуйста, не отвечайте на него.
Получатель: #EMAIL_TO#
```
## Описание типа события
```text
#EMAIL_TO# - e-mail получателя
#TRADE_ID# - ID сделки в системе
#SIDE# - направление (buy/sell)
#FIAT_AMOUNT# - сумма в фиатной валюте
#COUNTERPARTY_NAME# - имя контрагента
```
## Что вставлять в админке Битрикса
### Тип почтового события
- **Символьный код:** `REBIT_NOTIFICATION_TRADE_DISCOVERED`
- **Описание:**
```text
#EMAIL_TO# - E-mail получателя
#TRADE_ID# - ID сделки
#SIDE# - Направление сделки (buy/sell)
#FIAT_AMOUNT# - Сумма в фиатной валюте
#COUNTERPARTY_NAME# - Имя контрагента
```
### Почтовый шаблон
- **Кому:** `#EMAIL_TO#`
- **Тема:** `Rebit P2P — новая сделка обнаружена`
- **Тип письма:** HTML
- **Тело:** используйте HTML-шаблон выше
