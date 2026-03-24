# CLI-команды

CLI-команда это внешний вход в приложение, как контроллер, только для консоли.
Поэтому она должна быть тонкой: вызывать UseCase, а не содержать сценарий внутри себя.

## Где размещать

`lib/Presentation/Command/<Domain>/*Command.php`

## Целевой стандарт

- `extends RebitCommand` (`Rebit\Share\Presentation\Command\RebitCommand`);
- `#[AsCommand(...)]`;
- наследник реализует метод `handle()`, а не `execute()`;
- внутри `handle()` только вызов UseCase и форматирование вывода;
- возврат `Command::SUCCESS`, `Command::FAILURE` или `Command::INVALID`.

## Что даёт RebitCommand

`RebitCommand` — базовый класс для всех консольных команд. Он инкапсулирует бойлерплейт,
который раньше дублировался в каждой команде:

| Бойлерплейт | Как решает RebitCommand |
|---|---|
| `new SymfonyStyle($input, $output)` | Создаётся в `execute()`, передаётся в `handle($io, $input)` |
| `try/catch (\Throwable)` | Перехват в `execute()`, вывод ошибки через `$io->error()`, возврат `FAILURE` |
| Защита от параллельного запуска | Атрибут `#[WithLock]` — flock по имени команды |

Наследник реализует **только** `handle(SymfonyStyle $io, InputInterface $input): int`.

### Атрибут `#[WithLock]`

Команда, помеченная `#[WithLock]`, не допускает параллельный запуск.
Перед вызовом `handle()` захватывается flock. Если лок уже занят — команда
завершается с предупреждением `"Команда ... уже запущена"` и кодом `SUCCESS`.

```php
#[AsCommand(name: 'app:heavy-job', description: '...')]
#[WithLock]
final class HeavyJobCommand extends RebitCommand
{
    // ...
}
```

По умолчанию ключ лока = имя команды. Для кастомного ключа:

```php
#[WithLock(lockName: 'shared-lock-name')]
```

## Эталоны

- `api/public/local/modules/rebit.wallet/lib/Presentation/Command/Facet/FacetSkusIndexCommand.php` — без лока
- `api/public/local/modules/rebit.wallet/lib/Presentation/Command/Facet/FacetUpdateConsumerCommand.php` — с `#[WithLock]`

## Простая команда без параметров

```php
#[AsCommand(
    name: 'app:elastic:index-facet',
    description: 'Индексация активных товарных предложений в Elasticsearch',
)]
final class FacetSkusIndexCommand extends RebitCommand
{
    public function __construct(
        private readonly ReindexFacetUseCase $indexer,
    ) {
        parent::__construct();
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $io->text('Начинаю индексацию фасетов...');

        $this->indexer->execute();

        $io->text('Индексация завершена успешно');

        return Command::SUCCESS;
    }
}
```

Обработка ошибок не нужна — `RebitCommand` перехватывает `\Throwable`, выводит сообщение и возвращает `FAILURE`.

## Команда с параметрами

```php
#[AsCommand(
    name: 'app:product:reindex',
    description: 'Переиндексирует товары',
)]
final class ReindexProductCommand extends RebitCommand
{
    public function __construct(
        private readonly ReindexProductUseCase $reindexProductUseCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('iblock_id', InputArgument::REQUIRED, 'ID инфоблока');
        $this->addOption('batch-size', 'b', InputOption::VALUE_OPTIONAL, 'Размер порции', 100);
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $this->reindexProductUseCase->execute(
            iblockId: (int)$input->getArgument('iblock_id'),
            batchSize: (int)$input->getOption('batch-size'),
        );

        return Command::SUCCESS;
    }
}
```

## Команда-консьюмер с блокировкой

```php
#[AsCommand(
    name: 'app:facet:update-consume',
    description: 'Консьюмер очереди обновления фасета',
)]
#[WithLock]
final class FacetUpdateConsumerCommand extends RebitCommand
{
    public function __construct(
        private readonly RunFacetConsumerUseCase $runFacetConsumerUseCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Максимум сообщений', '500')
            ->addOption('time-limit', 't', InputOption::VALUE_REQUIRED, 'Лимит времени в секундах', '50')
        ;
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $limit = (int)$input->getOption('limit');
        $timeLimit = (int)$input->getOption('time-limit');

        $io->text(sprintf('Запуск консьюмера фасета (limit=%d, time-limit=%ds)', $limit, $timeLimit));

        $this->runFacetConsumerUseCase->execute(new RunFacetConsumerInputDto(
            type: FacetConsumerTypeEnum::UPDATE,
            limit: $limit,
            timeLimit: $timeLimit,
        ));

        $io->text('Консьюмер фасета завершён');

        return Command::SUCCESS;
    }
}
```

`#[WithLock]` гарантирует, что два cron-процесса не запустят один и тот же консьюмер параллельно.
Если нужно масштабировать — убрать атрибут.

## Регистрация и вызов

Команды автоматически регистрируются через атрибут `#[AsCommand(...)]`.

Вызов из CLI:

```bash
# Список всех команд
php local/bin/bitrix-console list

# Вызов команды
php local/bin/bitrix-console app:elastic:index-facet

# С параметрами
php local/bin/bitrix-console app:product:reindex 2 --batch-size=500
```

## Антипаттерны

- Бизнес-логика внутри команды.
- `ServiceLocator` вместо DI через конструктор.
- Переопределение `execute()` вместо `handle()`.
- Дублирование `try/catch` — ошибки уже перехватывает `RebitCommand`.
- Ручной лок через `LockFactory` — использовать `#[WithLock]`.

## Что нужно запомнить

1. CLI-команда — такой же тонкий адаптер как контроллер.
2. Наследуй `RebitCommand`, реализуй `handle()`.
3. Для эксклюзивного запуска — `#[WithLock]`.
4. Вся работа делается через UseCase.

---

<- [09. HTTP API: контроллеры и маршруты](09_http-api-контроллеры-и-маршруты.md) | [11. Кеширование](11_кеширование.md) ->

[^ К оглавлению](README.md)
