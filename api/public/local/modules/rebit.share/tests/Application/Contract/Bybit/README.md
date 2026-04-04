# BybitEnvironmentEnum tests

Локальный smoke-набор для проверки fallback URL в `BybitEnvironmentEnum`.

## Запуск

```bash
docker compose run --rm api-php-cli php vendor/bin/phpunit --colors=always public/local/modules/rebit.share/tests/Application/Contract/Bybit/BybitEnvironmentEnumTest.php
```
