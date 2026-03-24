<?php

/**
 * Minimal Bitrix stubs for PHPStan static analysis.
 */

namespace Bitrix\Main {

    class Application
    {
        /** @return static */
        public static function getInstance(): static {}
        public static function getConnection(): DB\Connection {}
        public static function getDocumentRoot(): string {}
        public function getManagedCache(): Data\ManagedCache {}
        public function getTaggedCache(): Data\TaggedCache {}
    }

    class Loader
    {
        public static function includeModule(string $moduleName): bool {}
    }

    class Response
    {
        public function setContent(?string $content): static {}
        public function getContent(): string {}
        public function setStatus(int|string $status): static {}
        public function getStatus(): string {}
    }

    class HttpResponse extends Response {}

    class HttpRequest
    {
        public function getHeader(string $name): ?string {}
        /** @return array<string, mixed>|null */
        public function getFile(string $name): ?array {}
        public function getPost(string $name): mixed {}
        public function getQueryList(): Type\ParameterDictionary {}
        public function getPostList(): Type\ParameterDictionary {}
        public function getJsonList(): Type\ParameterDictionary {}
    }

    class EventResult {}

    class Event
    {
        public function getParameter(string $name): mixed {}
    }

    class SystemException extends \Exception {}
    class ArgumentException extends \Exception {}
    class ObjectException extends \Exception {}
    class LoaderException extends \Exception {}
    class ArgumentTypeException extends ArgumentException {}
    class InvalidOperationException extends SystemException {}
    class ObjectPropertyException extends SystemException {}

    class Result
    {
        public function isSuccess(): bool {}
        /** @return array<string> */
        public function getErrorMessages(): array {}
    }
}

namespace Bitrix\Main\Engine {

    class Controller
    {
        public const SCOPE_AJAX = 'ajax';
        /** @var \Bitrix\Main\HttpRequest */
        public $request;
        public function setScope(string $scope): void {}
        public function setCurrentUser(CurrentUser $user): void {}
        public function getRequest(): \Bitrix\Main\HttpRequest {}
        /** @return array<string, mixed> */
        protected function getDefaultPreFilters(): array {}
        /** @return array<string, mixed> */
        protected function getDefaultPostFilters(): array {}
        protected function generateActionMethodName(string $action): string {}
        protected function create(string $actionName): Action|InlineAction|FallbackAction|null {}
        protected function runProcessingThrowable(\Throwable $throwable): void {}
        /** @return array<string, mixed> */
        protected function configureActions(): array {}
        public function setFrameMode(bool $mode): void {}
        public function finalizeResponse(\Bitrix\Main\Response $response): void {}
    }

    class Action {}
    class InlineAction extends Action {}
    class FallbackAction extends Action {}
    class CurrentUser
    {
        public static function get(): static {}
    }
    class ControllerBuilder {}
}

namespace Bitrix\Main\Engine\ActionFilter {
    class Base
    {
        public function __construct() {}
        public function onBeforeAction(\Bitrix\Main\Event $event): ?\Bitrix\Main\EventResult {}
        public function onAfterAction(\Bitrix\Main\Event $event): ?\Bitrix\Main\EventResult {}
    }
}

namespace Bitrix\Main\Engine\AutoWire {
    class Parameter
    {
        public function __construct(string $className, callable $constructor) {}
    }
}

namespace Bitrix\Main\Engine\Response {
    class Json extends \Bitrix\Main\HttpResponse
    {
        public function __construct(mixed $data = null) {}
    }
}

namespace Bitrix\Main\Type {
    class ParameterDictionary extends \ArrayObject
    {
        /** @return array<string, mixed> */
        public function getValues(): array {}
        /** @return array<string, mixed> */
        public function toArray(): array {}
    }

    class Date
    {
        public function format(string $format): string {}
    }
    class DateTime extends Date
    {
        public static function createFromTimestamp(int $timestamp): static {}
        public function getTimestamp(): int {}
        public function toString(): string {}
    }
}

namespace Bitrix\Main\Routing {
    class RoutingConfigurator
    {
        public function get(string $uri, mixed $controller): self {}
        public function post(string $uri, mixed $controller): self {}
        public function delete(string $uri, mixed $controller): self {}
        public function put(string $uri, mixed $controller): self {}
    }
}

namespace Bitrix\Main\ORM {
    class Entity
    {
        public function getDBTableName(): string {}
        public function getConnection(): \Bitrix\Main\DB\Connection {}
    }
}

namespace Bitrix\Main\ORM\Data {
    class DataManager
    {
        public static function query(): \Bitrix\Main\ORM\Query\Query {}
        public static function getEntity(): \Bitrix\Main\ORM\Entity {}
        public static function createObject(): \Bitrix\Main\ORM\Objectify\EntityObject {}
        /** @param array<string, mixed> $data */
        public static function add(array $data): AddResult {}
        /** @param array<string, mixed> $data */
        public static function update(int|string $primary, array $data): UpdateResult {}
        public static function getById(int|string $primary): \Bitrix\Main\ORM\Query\Result {}
        /** @param array<string, mixed> $parameters */
        public static function getList(array $parameters = []): \Bitrix\Main\ORM\Query\Result {}
    }
    class AddResult {}
    class UpdateResult {}
}

namespace Bitrix\Main\ORM\Query {
    class Query
    {
        /** @param array<string> $columns */
        public function setSelect(array $columns): self {}
        public function addSelect(string $field, ?string $alias = null): self {}
        /** @param array<string, mixed> $filter */
        public function setFilter(array $filter): self {}
        public function where(string $field, mixed ...$args): self {}
        /** @param array<string, string> $order */
        public function setOrder(array $order): self {}
        public function setLimit(int $limit): self {}
        public function setOffset(int $offset): self {}
        public function setCountTotal(bool $flag): self {}
        public function getCount(): int {}
        public function enablePrivateFields(): self {}
        public function exec(): Result {}
    }
    class Result
    {
        /** @return array<string, mixed>|false */
        public function fetch(): array|false {}
        /** @return array<int, array<string, mixed>> */
        public function fetchAll(): array {}
        public function fetchObject(): ?\Bitrix\Main\ORM\Objectify\EntityObject {}
        public function fetchCollection(): \Bitrix\Main\ORM\Objectify\Collection {}
    }
}

namespace Bitrix\Main\ORM\Objectify {
    class EntityObject
    {
        public function getId(): int {}
        public function save(): \Bitrix\Main\ORM\Data\Result {}
    }
}

namespace Bitrix\Main\ORM\Data {
    class Result extends \Bitrix\Main\Result {}
}

namespace Bitrix\Main\DB {
    class Connection
    {
        public function query(string $sql): mixed {}
        public function queryExecute(string $sql): void {}
    }
    class SqlQueryException extends \Bitrix\Main\SystemException {}
}

namespace Bitrix\Main\DI {
    class ServiceLocator
    {
        public static function getInstance(): static {}
        public function get(string $code): mixed {}
        public function has(string $code): bool {}
    }
}

namespace Bitrix\Main\Config {
    class Configuration
    {
        public static function getInstance(): static {}
        public static function getValue(string $name): mixed {}
        /** @return array<string, mixed> */
        public function get(string $name): array {}
    }
}

namespace Bitrix\Main\Web {
    class Json
    {
        /** @throws \Bitrix\Main\ArgumentException */
        public static function encode(mixed $data, int $options = 0): string {}
        /** @throws \Bitrix\Main\ArgumentException */
        public static function decode(string $json, bool $assoc = true): mixed {}
    }
    class HttpClient
    {
        public function get(string $url): ?string {}
        public function post(string $url, mixed $postData = null): ?string {}
        public function setHeader(string $name, string $value): void {}
        public function clearHeaders(): void {}
        public function setAuthorization(string $user, string $password = ''): void {}
        public function getStatus(): int {}
        /** @return array<string, string> */
        public function getError(): array {}
    }
}

namespace Bitrix\Main\Data {
    class ManagedCache
    {
        public function read(int $ttl, string $uniqueString, string $initDir = ''): bool {}
        public function get(string $uniqueString): mixed {}
        public function set(string $uniqueString, mixed $value): void {}
        public function clean(string $uniqueString, string $initDir = ''): void {}
    }
    class TaggedCache
    {
        public function startTagCache(string $path): void {}
        public function endTagCache(): void {}
        public function registerTag(string $tag): void {}
        public function clearByTag(string $tag): void {}
    }
    class Cache
    {
        public static function createInstance(): static {}
        public function noOutput(): void {}
        public function startDataCache(int $ttl = 0, string $uniqueString = '', string $initDir = ''): bool {}
        public function endDataCache(mixed $vars = null): void {}
        public function abortDataCache(): void {}
        /** @return array<string, mixed>|false */
        public function getVars(): array|false {}
        public function initCache(int $ttl, string $uniqueString, string $initDir = ''): bool {}
        public function forceRewriting(bool $mode): void {}
        public function clean(string $uniqueString, string $initDir = ''): void {}
        public function cleanDir(string $initDir = '', string $baseDir = 'cache'): void {}
    }
}

namespace Bitrix\Highloadblock {
    class HighloadBlockTable extends \Bitrix\Main\ORM\Data\DataManager
    {
        public static function compileEntity(mixed $hlblock): \Bitrix\Main\ORM\Entity {}
    }
}

namespace {
    class CModule
    {
        public function DoInstall(): void {}
        public function DoUninstall(): void {}
    }
    class CUser
    {
        /** @param array<string, mixed> $fields */
        public function Update(int $id, array $fields): bool {}
    }
    class CFile
    {
        /** @param array<string, mixed> $file */
        public static function SaveFile(array $file, string $module): int|false {}
        public static function GetPath(int $fileId): ?string {}
    }
    function RegisterModule(string $moduleId): void {}
    function UnRegisterModule(string $moduleId): void {}
    function getLocalPath(string $path): string|false {}

    /**
     * Стабы скомпилированных HL-блоков.
     * Классы генерируются в рантайме через HighloadBlockTable::compileEntity().
     */
    class RebitApiConnectionTable extends \Bitrix\Main\ORM\Data\DataManager {}
    class RebitBalanceTable extends \Bitrix\Main\ORM\Data\DataManager {}
    class RebitTransactionTable extends \Bitrix\Main\ORM\Data\DataManager {}
}

/**
 * Стабы EO_ классов Exchange-модуля для PHPStan.
 */
namespace Rebit\Exchange\Domain\Advertisement\Entity\Table {
    class EO_Advertisement extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfUserId(): int {}
        public function getUfBybitAdId(): string {}
        public function getUfCurrencyPairId(): int {}
        public function getUfSide(): string {}
        public function getUfPriceType(): string {}
        public function getUfPrice(): float {}
        public function getUfPremium(): float {}
        public function getUfQuantity(): float {}
        public function getUfQuantityRemaining(): float {}
        public function getUfMinAmount(): float {}
        public function getUfMaxAmount(): float {}
        public function getUfPaymentMethodIds(): string {}
        public function getUfPaymentPeriod(): int {}
        public function getUfFeeRate(): float {}
        public function getUfConditions(): ?string {}
        public function getUfChatScriptId(): int {}
        public function getUfStatus(): string {}
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function setUfUserId(int $v): static {}
        public function setUfBybitAdId(string $v): static {}
        public function setUfCurrencyPairId(int $v): static {}
        public function setUfSide(string $v): static {}
        public function setUfPriceType(string $v): static {}
        public function setUfPrice(float $v): static {}
        public function setUfPremium(float $v): static {}
        public function setUfQuantity(float $v): static {}
        public function setUfQuantityRemaining(float $v): static {}
        public function setUfMinAmount(float $v): static {}
        public function setUfMaxAmount(float $v): static {}
        public function setUfPaymentMethodIds(string $v): static {}
        public function setUfPaymentPeriod(int $v): static {}
        public function setUfFeeRate(float $v): static {}
        public function setUfConditions(string $v): static {}
        public function setUfChatScriptId(int $v): static {}
        public function setUfStatus(string $v): static {}
        public function setUfCreatedAt(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfUpdatedAt(?\Bitrix\Main\Type\DateTime $v): static {}
    }
    class EO_Advertisement_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Rebit\Exchange\Domain\Trade\Entity\Table {
    class EO_Trade extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfBybitOrderId(): string {}
        public function getUfBybitStatus(): int {}
        public function getUfBuyerUserId(): int {}
        public function getUfSellerUserId(): int {}
        public function getUfAdvertisementId(): int {}
        public function getUfOrderBookEntryId(): int {}
        public function getUfCurrencyPairId(): int {}
        public function getUfSide(): string {}
        public function getUfPrice(): float {}
        public function getUfQuantity(): float {}
        public function getUfFiatAmount(): float {}
        public function getUfFee(): float {}
        public function getUfPaymentMethodId(): int {}
        public function getUfPaymentDetails(): string {}
        public function getUfComment(): string {}
        public function getUfStatus(): string {}
        public function getUfCounterpartyName(): string {}
        public function getUfPaymentDeadline(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfPaidAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfConfirmedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfCompletedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfCancelledAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfCancelReason(): string {}
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function setUfBybitOrderId(string $v): static {}
        public function setUfBybitStatus(int $v): static {}
        public function setUfBuyerUserId(int $v): static {}
        public function setUfSellerUserId(int $v): static {}
        public function setUfAdvertisementId(int $v): static {}
        public function setUfOrderBookEntryId(int $v): static {}
        public function setUfCurrencyPairId(int $v): static {}
        public function setUfSide(string $v): static {}
        public function setUfPrice(float $v): static {}
        public function setUfQuantity(float $v): static {}
        public function setUfFiatAmount(float $v): static {}
        public function setUfFee(float $v): static {}
        public function setUfPaymentMethodId(int $v): static {}
        public function setUfPaymentDetails(string $v): static {}
        public function setUfComment(string $v): static {}
        public function setUfStatus(string $v): static {}
        public function setUfCounterpartyName(string $v): static {}
        public function setUfPaymentDeadline(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfPaidAt(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfConfirmedAt(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfCompletedAt(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfCancelledAt(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfCancelReason(string $v): static {}
        public function setUfUpdatedAt(?\Bitrix\Main\Type\DateTime $v): static {}
    }
    class EO_Trade_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Rebit\Exchange\Domain\TradeChat\Entity\Table {
    class EO_TradeMessage extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfTradeId(): int {}
        public function getUfUserId(): int {}
        public function getUfMessage(): string {}
        public function getUfMessageType(): string {}
        public function getUfContentType(): string {}
        public function getUfBybitMsgUuid(): string {}
        public function getUfFileName(): string {}
        public function getUfScriptStepId(): int {}
        public function getUfIsRead(): int {}
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function setUfTradeId(int $v): static {}
        public function setUfUserId(int $v): static {}
        public function setUfMessage(string $v): static {}
        public function setUfMessageType(string $v): static {}
        public function setUfContentType(string $v): static {}
        public function setUfBybitMsgUuid(string $v): static {}
        public function setUfFileName(string $v): static {}
        public function setUfScriptStepId(int $v): static {}
        public function setUfIsRead(int $v): static {}
        public function setUfCreatedAt(?\Bitrix\Main\Type\DateTime $v): static {}
    }
    class EO_TradeMessage_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Rebit\Exchange\Domain\Currency\Entity\Table {
    class EO_Currency extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfCode(): string {}
        public function getUfName(): string {}
        public function getUfType(): string {}
        public function getUfDecimals(): int {}
        public function getUfIcon(): int {}
        public function getUfIsActive(): bool {}
        public function getUfSort(): int {}
    }
    class EO_CurrencyPair extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfTokenCurrencyId(): int {}
        public function getUfFiatCurrencyId(): int {}
        public function getUfCode(): string {}
        public function getUfIsActive(): bool {}
        public function getUfIsDefault(): bool {}
        public function getUfSort(): int {}
    }
    class EO_Currency_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
    class EO_CurrencyPair_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table {
    class EO_ChatScript extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfUserId(): int {}
        public function getUfName(): string {}
        public function getUfIsActive(): int {}
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function setUfUserId(int $v): static {}
        public function setUfName(string $v): static {}
        public function setUfIsActive(int $v): static {}
        public function setUfCreatedAt(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfUpdatedAt(?\Bitrix\Main\Type\DateTime $v): static {}
    }
    class EO_ChatScriptStep extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfScriptId(): int {}
        public function getUfSort(): int {}
        public function getUfMessage(): string {}
        public function getUfDelaySeconds(): int {}
        public function setUfScriptId(int $v): static {}
        public function setUfSort(int $v): static {}
        public function setUfMessage(string $v): static {}
        public function setUfDelaySeconds(int $v): static {}
    }
    class EO_ChatScript_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
    class EO_ChatScriptStep_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Rebit\Exchange\Domain\OrderBook\Entity\Table {
    class EO_OrderBookEntry extends \Bitrix\Main\ORM\Objectify\EntityObject {}
    class EO_OrderBookEntry_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Rebit\Exchange\Domain\PaymentMethod\Entity\Table {
    class EO_PaymentMethod extends \Bitrix\Main\ORM\Objectify\EntityObject {}
    class EO_PaymentMethod_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Rebit\Wallet\Domain\Balance\Entity\Table {
    class EO_Balance extends \Bitrix\Main\ORM\Objectify\EntityObject
    {
        public function getUfUserId(): int {}
        public function getUfCurrencyId(): int {}
        public function getUfAvailable(): float {}
        public function getUfLocked(): float {}
        public function getUfTotal(): float {}
        public function getUfSyncedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime {}
        public function setUfUserId(int $v): static {}
        public function setUfCurrencyId(int $v): static {}
        public function setUfAvailable(float $v): static {}
        public function setUfLocked(float $v): static {}
        public function setUfTotal(float $v): static {}
        public function setUfSyncedAt(?\Bitrix\Main\Type\DateTime $v): static {}
        public function setUfUpdatedAt(?\Bitrix\Main\Type\DateTime $v): static {}
    }
    class EO_Balance_Collection extends \Bitrix\Main\ORM\Objectify\Collection {}
}

namespace Bitrix\Main\ORM\Objectify {
    /**
     * @implements \ArrayAccess<int, EntityObject>
     * @implements \Iterator<int, EntityObject>
     */
    class Collection implements \Countable, \IteratorAggregate, \ArrayAccess, \Iterator
    {
        /** @return array<EntityObject> */
        public function getAll(): array {}
        public function count(): int {}
        public function getIterator(): \ArrayIterator {}
        public function current(): EntityObject {}
        public function next(): void {}
        public function key(): int {}
        public function valid(): bool {}
        public function rewind(): void {}
        public function offsetExists(mixed $offset): bool {}
        public function offsetGet(mixed $offset): ?EntityObject {}
        public function offsetSet(mixed $offset, mixed $value): void {}
        public function offsetUnset(mixed $offset): void {}
    }
}
