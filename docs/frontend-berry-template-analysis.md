# Анализ шаблона Berry для проекта `frontend`

Дата: 2026-03-25

## Цель

Понять, какие части купленного шаблона `berry-vuertify-vuejs-1.7.0` имеет смысл вернуть в текущий проект `frontend`, а какие лучше оставить за бортом как демо/шаблонный шум.

---

## Что сравнивалось

- Текущий проект: `frontend/src`
- Шаблон: `C:\Users\User\Downloads\berry-vuertify-vuejs-1.7.0\berry-vuertify-vuejs-1.7.0\full-version\src`
- Дополнительно просмотрен `seed/src` как минимальная база Berry

---

## Короткий вывод

Текущий `frontend` уже является урезанной и адаптированной версией Berry.

То есть:
- **архитектурную основу возвращать не нужно** — она уже есть в проекте;
- **возвращать стоит только отдельные reusable-части**, которые действительно улучшат UI и не притащат лишний шаблонный оверкод.

Сейчас в проекте уже сохранены ключевые части Berry:

- `layouts/full/*`
- `layouts/blank/*`
- `router/index.ts`
- `stores/customizer.ts`
- `plugins/vuetify.ts`
- вертикальный sidebar/header
- theme-структура

Это нормальный компромисс: **Berry как UI-shell + собственные доменные страницы сверху**.

---

## Что уже есть и не требует возврата

### Layout / Theme / Router

Уже присутствуют и выглядят достаточными:

- `frontend/src/layouts/full/FullLayout.vue`
- `frontend/src/layouts/blank/BlankLayout.vue`
- `frontend/src/router/index.ts`
- `frontend/src/router/MainRoutes.ts`
- `frontend/src/router/PublicRoutes.ts`
- `frontend/src/plugins/vuetify.ts`
- `frontend/src/stores/customizer.ts`

### Что это значит

Возвращать весь Berry обратно не нужно. Базовый shell уже сохранён.

---

## Что реально полезно вернуть

## 1. Shared card wrappers

Сейчас папка `frontend/src/components/shared/` пустая, хотя в Berry там лежат хорошие простые reusable-компоненты.

### Полезные кандидаты

- `components/shared/UiParentCard.vue`
- `components/shared/UiTableCard.vue`
- `components/shared/WidgetCard.vue`
- `components/shared/WidgetCardv2.vue`

### Зачем это нужно

Сейчас многие страницы собираются напрямую на `v-card`, `v-table`, заголовках и `div`:

- `frontend/src/views/dashboard/DashboardPage.vue`
- `frontend/src/views/wallet/TransactionsPage.vue`
- `frontend/src/views/exchange/OrderBookPage.vue`
- страницы профиля

Из-за этого нет общего UI-слоя, и разметка начинает дублироваться.

### Практическая польза

Если вернуть эти обёртки, проект получит:

- единый стиль карточек;
- единый шаблон таблиц;
- меньше повторения Vuetify-разметки;
- более чистый page composition.

### Вердикт

**Стоит вернуть.**

Но не всё подряд, а именно как основу для собственного `shared`-слоя.

---

## 2. Паттерн empty state

В Berry есть `views/ui-elements/advance/UiEmptyState.vue`.

Сама demo-страница не нужна, но полезен сам паттерн работы с `v-empty-state`.

### Где применить в проекте

- пустой стакан;
- пустые балансы;
- пустые транзакции;
- не подключён Bybit;
- не пришли методы оплаты или валютные пары;
- состояния ошибок загрузки.

### Что лучше сделать

Не переносить demo-page, а создать свой проектный компонент, например:

- `frontend/src/components/shared/AppEmptyState.vue`

### Вердикт

**Стоит вернуть как идею и оформить своим компонентом.**

---

## 3. Паттерн таблиц и server-side table

В Berry есть хорошие reference-страницы:

- `views/ui-elements/datatables/BasicDataTable.vue`
- `views/ui-elements/datatables/ServersideTable.vue`

Они не нужны как готовые страницы, но полезны как источник паттернов.

### Почему это важно

В проекте уже есть и будут таблицы:

- `TransactionsPage.vue`
- будущие списки объявлений;
- будущая история сделок;
- возможно списки ордеров, операций, логов пользователя.

### Что взять из идеи

- декларативные `headers`;
- controlled sort state;
- controlled loading state;
- reusable toolbar с фильтрами;
- единый table shell.

### Что лучше сделать у себя

Собрать собственный набор компонентов:

- `AppTableCard.vue`
- `AppTableToolbar.vue`
- общий паттерн для сортировки / фильтров / loading / empty state

### Вердикт

**Очень полезно вернуть как архитектурный паттерн, но не как копию demo-page.**

---

## 4. Breadcrumb / page header

В Berry есть:

- `components/shared/BaseBreadcrumb.vue`

### Когда это полезно

Если хочется выровнять верхнюю часть внутренних страниц:

- кошелёк
- профиль
- дашборд
- exchange

### Когда можно не брать

Если проекту сейчас достаточно простых `<h2>` и breadcrumb не нужен по UX.

### Вердикт

**Опционально.** Полезно, но не приоритет №1.

---

## 5. Vuetify labs-компоненты из шаблона

В Berry в `plugins/vuetify.ts` дополнительно подключены:

- `VFileUpload`
- `VFileUploadItem`
- `VPie`
- `VCalendar`
- `VMaskInput`

### Что реально может пригодиться

#### `VMaskInput`
Полезно для:
- телефона;
- реквизитов;
- платёжных форм;
- масок ввода.

#### `VFileUpload`
Полезно, если будут:
- KYC-документы;
- вложения в чат сделки;
- загрузка подтверждений.

### Что пока не нужно

- `VPie`
- `VCalendar`

если под это нет ближайших задач.

### Вердикт

**Точечно полезно.** Возвращать только под конкретный сценарий.

---

## Что возвращать не стоит

## 1. Полный auth-блок Berry

В шаблоне есть множество вариантов:

- `views/authentication/auth1/*`
- `views/authentication/auth2/*`
- `views/authentication/auth3/*`
- forgot/reset/check-mail/code-verification flows

### Почему не стоит

У проекта уже свой auth-flow. Возврат шаблонных auth-страниц даст:

- лишние маршруты;
- лишние assets;
- лишнюю поддержку;
- путаницу в UX.

### Вердикт

**Не возвращать.**

---

## 2. Landing / pricing / demo pages

В Berry есть:

- `views/pages/landingpage/*`
- `views/pages/pricing/*`
- `views/pages/maintenance/*`

### Вердикт

**Не возвращать**, если не планируется отдельный маркетинговый сайт на этом же фронте.

---

## 3. Demo apps: ecommerce / chat / mail / kanban

Это шаблонный мусор для текущего продукта.

### Вердикт

**Не возвращать вообще.**

---

## 4. Horizontal layout / customizer playground / mega menu / language dropdown

В Berry есть полноценный theme playground:

- horizontal layout;
- customizer panel;
- search panel;
- language dropdown;
- mega menu.

Часть этой инфраструктуры физически ещё лежит и в текущем проекте, но уже упрощена.

### Вердикт

**Возвращать не нужно.**

Скорее наоборот — в будущем можно аккуратно чистить оставшиеся шаблонные хвосты.

---

## Самые полезные кандидаты на возврат — по приоритету

## Приоритет 1

### Вернуть как shared-base

- `UiParentCard.vue`
- `UiTableCard.vue`
- `WidgetCard.vue` или `WidgetCardv2.vue`

**Польза:** высокая  
**Цена интеграции:** низкая

---

## Приоритет 2

### Сделать единый empty state

Не переносить demo-page, а сделать свой `AppEmptyState.vue`.

Использование:
- `TransactionsPage.vue`
- `BalancesPage.vue`
- `OrderBookPage.vue`
- `DashboardPage.vue`

**Польза:** высокая  
**Цена интеграции:** низкая

---

## Приоритет 3

### Собрать project-specific table pattern

Опираться на идеи из:
- `BasicDataTable.vue`
- `ServersideTable.vue`

Но реализовать своими компонентами и под свои данные.

**Польза:** высокая  
**Цена интеграции:** средняя

---

## Приоритет 4

### Подключить breadcrumbs, если нужна унификация page headers

**Польза:** средняя  
**Цена интеграции:** низкая

---

## Приоритет 5

### Точечно вернуть `VMaskInput` / `VFileUpload`

Только под реальные продуктовые задачи.

**Польза:** ситуативная  
**Цена интеграции:** низкая / средняя

---

## Что можно использовать только как reference

Из dashboard-части Berry полезны не сами demo-виджеты, а:

- композиция KPI-карточек;
- action cards;
- summary widgets;
- визуальные паттерны для dashboard.

### Где это пригодится

Для развития:
- `frontend/src/views/dashboard/DashboardPage.vue`

### Что не нужно переносить 1-в-1

- chart-heavy widgets;
- `apexcharts` только ради демо;
- fake revenue / stocks / ecommerce карточки.

---

## Практический итог

### Возвращать стоит

1. shared card wrappers;
2. empty-state pattern;
3. table pattern;
4. возможно breadcrumbs;
5. возможно `VMaskInput` / `VFileUpload`.

### Возвращать не стоит

1. auth demo pages;
2. landing/pricing/demo pages;
3. dashboard widgets 1-в-1;
4. apps/chat/ecommerce/mail;
5. heavy template infrastructure.

---

## Самый рациональный план на будущее

Если делать аккуратно и без оверкодинга, то оптимальный порядок такой:

1. создать `frontend/src/components/shared/`;
2. вернуть туда 2–4 простых card-wrapper компонента;
3. сделать свой `AppEmptyState.vue`;
4. затем собрать общий table shell / toolbar;
5. только потом думать про декоративные улучшения dashboard.

---

## Отдельное наблюдение

Для анализа "что вернуть" полезнее `full-version`, а для анализа "что упростить" — `seed`.

То есть:
- `full-version` — донор reusable UI;
- `seed` — эталон минимальной оболочки без демо-мусора.

Это может пригодиться позже для чистки оставшихся шаблонных хвостов в проекте.

---

## Итоговый вердикт

В Berry есть полезные вещи, но **возвращать нужно не шаблон, а только небольшой reusable-слой**.

Лучший кандидат на ближайшую доработку:

- `shared`-карточки;
- empty-state;
- table-pattern.

Именно это даст реальную пользу проекту без лишнего шаблонного балласта.
