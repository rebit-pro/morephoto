<?php

declare(strict_types=1);

/**
 * Рабочие стабы Bitrix-классов для PHPUnit-тестов.
 * Подключаются в tests/bootstrap.php.
 */

namespace Bitrix\Main\Type;

if (!class_exists(Date::class)) {
    class Date
    {
        protected int $timestamp;

        public function __construct(string $date = '', string $format = '')
        {
            $this->timestamp = ('' !== $date) ? (int)strtotime($date) : time();
        }

        public function format(string $format): string
        {
            return date($format, $this->timestamp);
        }

        public function getTimestamp(): int
        {
            return $this->timestamp;
        }
    }
}

if (!class_exists(DateTime::class)) {
    class DateTime extends Date
    {
        public static function createFromTimestamp(int $timestamp): static
        {
            $instance = new static();
            $instance->timestamp = $timestamp;

            return $instance;
        }

        public function toString(): string
        {
            return date('d.m.Y H:i:s', $this->timestamp);
        }
    }
}

namespace Bitrix\Main\Web;

if (!class_exists(Json::class)) {
    class Json
    {
        public static function encode(mixed $data): string
        {
            return (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        public static function decode(string $data): mixed
        {
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }
    }
}

/**
 * Стабы Bitrix ORM — нужны, т.к. EO_*-классы генерируются только в рантайме Bitrix.
 */
namespace Bitrix\Main;

if (!class_exists(Result::class)) {
    class Result
    {
        public function isSuccess(): bool { return true; }
        /** @return array<string> */
        public function getErrorMessages(): array { return []; }
    }
}

if (!class_exists(SystemException::class)) {
    class SystemException extends \RuntimeException {}
}

if (!class_exists(Application::class)) {
    class Application
    {
        private static ?self $instance = null;

        public static function getInstance(): self
        {
            return self::$instance ??= new self();
        }

        public static function getDocumentRoot(): string
        {
            return sys_get_temp_dir();
        }

        public function getTaggedCache(): Data\TaggedCache
        {
            return new Data\TaggedCache();
        }
    }
}

namespace Bitrix\Main\Data;

if (!class_exists(Cache::class)) {
    /**
     * Стаб DataCache — в тестах всегда кэш-промах, callback выполняется напрямую.
     */
    class Cache
    {
        public static function createInstance(): self { return new self(); }
        public function noOutput(): void {}
        public function startDataCache(int $ttl = 0, string $key = '', string $dir = ''): bool { return true; }
        public function endDataCache(mixed $vars = null): void {}
        public function abortDataCache(): void {}
        public function getVars(): mixed { return null; }
    }
}

if (!class_exists(ManagedCache::class)) {
    class ManagedCache {}
}

if (!class_exists(TaggedCache::class)) {
    class TaggedCache {}
}

namespace Bitrix\Main\Config;

if (!class_exists(Configuration::class)) {
    class Configuration
    {
        private static ?self $instance = null;

        public static function getInstance(): self
        {
            return self::$instance ??= new self();
        }

        public function get(string $name): mixed
        {
            return null;
        }
    }
}


namespace Bitrix\Main\ORM\Data;

if (!class_exists(Result::class)) {
    class Result extends \Bitrix\Main\Result {}
}

namespace Rebit\Identity\Domain\ApiConnection\Entity\Table;

if (!class_exists(EO_ApiConnection::class)) {
    class EO_ApiConnection
    {
        public function getId(): int { return 0; }
        public function getUfUserId(): int { return 0; }
        public function getUfApiKeyEncrypted(): string { return ''; }
        public function getUfSecretKeyEncrypted(): string { return ''; }
        public function getUfMode(): string { return ''; }
        public function getUfStatus(): string { return ''; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfLastVerifiedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfErrorMessage(): ?string { return null; }
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function setUfUserId(int $value): static { return $this; }
        public function setUfApiKeyEncrypted(string $value): static { return $this; }
        public function setUfSecretKeyEncrypted(string $value): static { return $this; }
        public function setUfMode(string $value): static { return $this; }
        public function setUfStatus(string $value): static { return $this; }
        public function setUfCreatedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function setUfLastVerifiedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function setUfErrorMessage(?string $value): static { return $this; }
        public function setUfUpdatedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function get(string $fieldName): mixed { return null; }
        public function set(string $fieldName, mixed $value): static { return $this; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

/**
 * Стабы Exchange-модуля: EO_ классы.
 */
namespace Rebit\Exchange\Domain\Advertisement\Entity\Table;

if (!class_exists(EO_Advertisement::class)) {
    class EO_Advertisement
    {
        public function getId(): int { return 0; }
        public function getUfUserId(): int { return 0; }
        public function getUfBybitAdId(): string { return ''; }
        public function getUfCurrencyPairId(): int { return 0; }
        public function getUfSide(): string { return ''; }
        public function getUfPriceType(): string { return ''; }
        public function getUfPrice(): float { return 0.0; }
        public function getUfPremium(): float { return 0.0; }
        public function getUfQuantity(): float { return 0.0; }
        public function getUfQuantityRemaining(): float { return 0.0; }
        public function getUfMinAmount(): float { return 0.0; }
        public function getUfMaxAmount(): float { return 0.0; }
        public function getUfPaymentMethodIds(): string { return ''; }
        public function getUfPaymentPeriod(): int { return 0; }
        public function getUfFeeRate(): float { return 0.0; }
        public function getUfConditions(): ?string { return null; }
        public function getUfChatScriptId(): int { return 0; }
        public function getUfStatus(): string { return ''; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function setUfStatus(string $value): static { return $this; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_Advertisement_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_Advertisement> */
    class EO_Advertisement_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_Advertisement> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

namespace Rebit\Exchange\Domain\Trade\Entity\Table;

if (!class_exists(EO_Trade::class)) {
    class EO_Trade
    {
        public function getId(): int { return 0; }
        public function getUfBybitOrderId(): string { return ''; }
        public function getUfBybitStatus(): int { return 0; }
        public function getUfBuyerUserId(): int { return 0; }
        public function getUfSellerUserId(): int { return 0; }
        public function getUfAdvertisementId(): int { return 0; }
        public function getUfCurrencyPairId(): int { return 0; }
        public function getUfSide(): string { return ''; }
        public function getUfPrice(): float { return 0.0; }
        public function getUfQuantity(): float { return 0.0; }
        public function getUfFiatAmount(): float { return 0.0; }
        public function getUfFee(): float { return 0.0; }
        public function getUfStatus(): string { return ''; }
        public function getUfCounterpartyName(): string { return ''; }
        public function getUfPaymentDeadline(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfPaidAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfCompletedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfCancelledAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfCancelReason(): string { return ''; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function setUfBybitStatus(int $value): static { return $this; }
        public function setUfStatus(string $value): static { return $this; }
        public function setUfPaidAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function setUfCompletedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function setUfUpdatedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_Trade_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_Trade> */
    class EO_Trade_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_Trade> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

namespace Rebit\Exchange\Domain\TradeChat\Entity\Table;

if (!class_exists(EO_TradeMessage::class)) {
    class EO_TradeMessage
    {
        public function getId(): int { return 0; }
        public function getUfTradeId(): int { return 0; }
        public function getUfUserId(): int { return 0; }
        public function getUfMessage(): string { return ''; }
        public function getUfMessageType(): string { return ''; }
        public function getUfContentType(): string { return ''; }
        public function getUfBybitMsgUuid(): string { return ''; }
        public function getUfFileName(): string { return ''; }
        public function getUfScriptStepId(): int { return 0; }
        public function getUfIsRead(): int { return 0; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_TradeMessage_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_TradeMessage> */
    class EO_TradeMessage_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_TradeMessage> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

namespace Rebit\Exchange\Domain\Currency\Entity\Table;

if (!class_exists(EO_CurrencyPair::class)) {
    class EO_CurrencyPair
    {
        public function getId(): int { return 0; }
        public function getUfTokenCurrencyId(): int { return 0; }
        public function getUfFiatCurrencyId(): int { return 0; }
        public function getUfCode(): string { return ''; }
        public function getUfIsActive(): bool { return true; }
        public function getUfIsDefault(): bool { return false; }
        public function getUfSort(): int { return 0; }
    }
}

if (!class_exists(EO_CurrencyPair_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_CurrencyPair> */
    class EO_CurrencyPair_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_CurrencyPair> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

if (!class_exists(EO_Currency::class)) {
    class EO_Currency
    {
        public function getId(): int { return 0; }
        public function getUfCode(): string { return ''; }
        public function getUfName(): string { return ''; }
        public function getUfType(): string { return ''; }
        public function getUfDecimals(): int { return 0; }
        public function getUfSort(): int { return 0; }
    }
}

if (!class_exists(EO_Currency_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_Currency> */
    class EO_Currency_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_Currency> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

namespace Rebit\Exchange\Domain\OrderBook\Entity\Table;

if (!class_exists(EO_OrderBookEntry::class)) {
    class EO_OrderBookEntry
    {
        public function getId(): int { return 0; }
        public function getUfBybitOrderId(): string { return ''; }
        public function getUfCurrencyPairId(): int { return 0; }
        public function getUfSide(): string { return ''; }
        public function getUfPrice(): float { return 0.0; }
        public function getUfQuantity(): float { return 0.0; }
        public function getUfMinAmount(): float { return 0.0; }
        public function getUfMaxAmount(): float { return 0.0; }
        public function getUfCounterpartyName(): string { return ''; }
        public function getUfCounterpartyRating(): float { return 0.0; }
        public function getUfCounterpartyTrades(): int { return 0; }
        public function getUfCounterpartyCompletionRate(): float { return 0.0; }
        public function getUfPaymentMethodIds(): string { return ''; }
        public function getUfPaymentTimeLimit(): int { return 0; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_OrderBookEntry_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_OrderBookEntry> */
    class EO_OrderBookEntry_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_OrderBookEntry> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

namespace Rebit\Exchange\Domain\PaymentMethod\Entity\Table;

if (!class_exists(EO_PaymentMethod::class)) {
    class EO_PaymentMethod
    {
        public function getId(): int { return 0; }
        public function getUfCode(): string { return ''; }
        public function getUfName(): string { return ''; }
        public function getUfSort(): int { return 0; }
        public function getUfIsActive(): int { return 0; }
    }
}

if (!class_exists(EO_PaymentMethod_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_PaymentMethod> */
    class EO_PaymentMethod_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_PaymentMethod> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

namespace Rebit\Exchange\Domain\ChatScript\Entity\Table;

if (!class_exists(EO_ChatScript::class)) {
    class EO_ChatScript
    {
        public function getId(): int { return 0; }
        public function getUfUserId(): int { return 0; }
        public function getUfName(): string { return ''; }
        public function getUfIsActive(): int { return 0; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function setUfName(string $value): static { return $this; }
        public function setUfIsActive(int $value): static { return $this; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_ChatScriptStep::class)) {
    class EO_ChatScriptStep
    {
        public function getId(): int { return 0; }
        public function getUfScriptId(): int { return 0; }
        public function getUfSort(): int { return 0; }
        public function getUfMessage(): string { return ''; }
        public function getUfDelaySeconds(): int { return 0; }
    }
}

if (!class_exists(EO_ChatScript_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_ChatScript> */
    class EO_ChatScript_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_ChatScript> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

if (!class_exists(EO_ChatScriptStep_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_ChatScriptStep> */
    class EO_ChatScriptStep_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_ChatScriptStep> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

if (!class_exists(EO_ChatScriptExecution::class)) {
    class EO_ChatScriptExecution
    {
        public function getId(): int { return 0; }
        public function getUfTradeId(): int { return 0; }
        public function getUfScriptId(): int { return 0; }
        public function getUfUserId(): int { return 0; }
        public function getUfLastStepSort(): int { return 0; }
        public function getUfStatus(): string { return ''; }
        public function getUfNextRunAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function setUfLastStepSort(int $value): static { return $this; }
        public function setUfStatus(string $value): static { return $this; }
        public function setUfNextRunAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_ChatScriptExecution_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_ChatScriptExecution> */
    class EO_ChatScriptExecution_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_ChatScriptExecution> */
        private array $items = [];
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

/**
 * Стабы Wallet-модуля: EO_ классы.
 */
namespace Rebit\Wallet\Domain\Balance\Entity\Table;

if (!class_exists(EO_Balance::class)) {
    class EO_Balance
    {
        public function getId(): int { return 0; }
        public function getUfUserId(): int { return 0; }
        public function getUfCurrencyId(): int { return 0; }
        public function getUfAvailable(): float { return 0.0; }
        public function getUfLocked(): float { return 0.0; }
        public function getUfTotal(): float { return 0.0; }
        public function getUfSyncedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function getUfUpdatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function setUfUserId(int $value): static { return $this; }
        public function setUfCurrencyId(int $value): static { return $this; }
        public function setUfAvailable(float $value): static { return $this; }
        public function setUfLocked(float $value): static { return $this; }
        public function setUfTotal(float $value): static { return $this; }
        public function setUfSyncedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function setUfUpdatedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_Balance_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_Balance> */
    class EO_Balance_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_Balance> */
        private array $items = [];
        public function getAll(): array { return $this->items; }
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}

namespace Rebit\Wallet\Domain\Transaction\Entity\Table;

if (!class_exists(EO_Transaction::class)) {
    class EO_Transaction
    {
        public function getId(): int { return 0; }
        public function getUfUserId(): int { return 0; }
        public function getUfCurrencyId(): int { return 0; }
        public function getUfType(): string { return ''; }
        public function getUfAmount(): float { return 0.0; }
        public function getUfBalanceAfter(): float { return 0.0; }
        public function getUfTradeId(): ?int { return null; }
        public function getUfDescription(): ?string { return null; }
        public function getUfBybitTxId(): ?string { return null; }
        public function getUfCreatedAt(): ?\Bitrix\Main\Type\DateTime { return null; }
        public function setUfUserId(int $value): static { return $this; }
        public function setUfCurrencyId(int $value): static { return $this; }
        public function setUfType(string $value): static { return $this; }
        public function setUfAmount(float $value): static { return $this; }
        public function setUfBalanceAfter(float $value): static { return $this; }
        public function setUfTradeId(?int $value): static { return $this; }
        public function setUfDescription(?string $value): static { return $this; }
        public function setUfBybitTxId(?string $value): static { return $this; }
        public function setUfCreatedAt(?\Bitrix\Main\Type\DateTime $value): static { return $this; }
        public function save(): \Bitrix\Main\ORM\Data\Result { return new \Bitrix\Main\ORM\Data\Result(); }
    }
}

if (!class_exists(EO_Transaction_Collection::class)) {
    /** @implements \ArrayAccess<int, EO_Transaction> */
    class EO_Transaction_Collection implements \IteratorAggregate, \ArrayAccess, \Countable
    {
        /** @var array<int, EO_Transaction> */
        private array $items = [];
        public function getAll(): array { return $this->items; }
        public function getIterator(): \ArrayIterator { return new \ArrayIterator($this->items); }
        public function count(): int { return count($this->items); }
        public function offsetExists(mixed $offset): bool { return isset($this->items[$offset]); }
        public function offsetGet(mixed $offset): mixed { return $this->items[$offset] ?? null; }
        public function offsetSet(mixed $offset, mixed $value): void { if (null === $offset) { $this->items[] = $value; } else { $this->items[$offset] = $value; } }
        public function offsetUnset(mixed $offset): void { unset($this->items[$offset]); }
    }
}
