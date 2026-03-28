# Rebit P2P

Rebit — P2P-платформа для работы с торговлей через Bybit. Проект помогает пользователю подключить API-ключи, смотреть стакан P2P-объявлений, отслеживать балансы и транзакции, а в следующих итерациях — управлять сделками и чатом сделки.

## Состав проекта

- `frontend/` — клиент на Vue 3 + Vuetify
- `api/` — backend на PHP 8.4, Bitrix D7
- `docs/` — предметная область, сценарии и архитектурные правила
- `docker-compose.yml` — локальное окружение

## Основной стек

- Vue 3, TypeScript, Vuetify
- PHP 8.4, Bitrix D7
- MySQL, Redis, RabbitMQ
- Docker / Docker Compose

## Что уже есть в UI

- дашборд
- P2P-стакан
- балансы
- транзакции
- профиль и подключение Bybit

## Что полезно взять из Berry

- shared card/table wrappers
- единый empty state
- table pattern для списков и истории
- layout чата для будущих деталей сделки
- status timeline и page header patterns

## Полезные документы

- `docs/designer-brief.md`
- `docs/domain.md`
- `docs/scenario.md`
- `docs/modules.md`
- `docs/devops-requirements-p2p-chat.md`
- `docs/frontend-design-assets-and-berry-shortlist.md`

Проект является внутренней разработкой Rebit.
