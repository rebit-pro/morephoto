<?php

declare(strict_types=1);

namespace Rebit\Share\Shared\Facade;

use Bitrix\Main\Application;
use Bitrix\Main\Data\ManagedCache;
use Bitrix\Main\SystemException;
use Bitrix\Main\Data\Cache as DataCache;
use Bitrix\Main\Data\TaggedCache;

final class Cache
{
    private const int DEFAULT_TTL = 3600;

    private static ?ManagedCache $managedCache = null;

    private static ?TaggedCache $taggedCache = null;

    private static ?DataCache $dataCache = null;

    /**
     * Заменяет кеш-инстанс (например, в тестах можно подставить mock).
     */
    public static function setCache(?ManagedCache $cache): void
    {
        self::$managedCache = $cache;
    }

    public static function setTaggedCache(?TaggedCache $taggedCache): void
    {
        self::$taggedCache = $taggedCache;
    }

    public static function setDataCache(?DataCache $dataCache): void
    {
        self::$dataCache = $dataCache;
    }

    /**
     * Возвращает результат $getDataCallback из кеша. При необходимости сохраняет его в кеш.
     *
     * Пример использования:
     *
     * ```
     * $favoriteIds = [1,2,3];
     * $products = CacheHelper::remember(
     *      static function() use ($favoriteIds){
     *          $shortProducts = new \Catalog\ShortProducts($favoriteIds);
     *
     *          return $shortProducts->getProductData();
     *      },
     *      'products' . __METHOD__ . implode('', $favoriteIds)  // генерирование уникального имени кеша
     * );
     * ```
     *
     * @throws SystemException
     */
    public static function remember(callable $getDataCallback, string $cacheId, int $ttl = self::DEFAULT_TTL): mixed
    {
        if ('' === $cacheId) {
            throw new SystemException('Cache id must not be empty.');
        }

        $cache = self::getManagedCache();

        if ($cache->read($ttl, $cacheId)) {
            return $cache->get($cacheId);
        }

        $data = $getDataCallback();
        $cache->set($cacheId, $data);

        return $data;
    }

    /**
     * Возвращает значение ключа кешированного ассоциативного массива. Если ключа нет, то добавляет его в массив.
     *
     * Пример:
     *
     * ```
     * $productId = CacheHelper::rememberArrayValue(
     *      static function (string $code, int $iblockId): ?int {
     *          $entity = \Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass();
     *          $row = $entity::query()
     *              ->setSelect(['ID'])
     *              ->where('CODE', $code)
     *              ->exec()
     *              ->fetch()
     *          ;
     *
     *          return (int)$row['ID'] ?? null;
     *      },
     *      cacheId: 'product_code_to_id_map',
     *      key: $code,
     *      ttl: 3600,
     * );
     *```
     *
     * @throws SystemException
     */
    public static function rememberArrayValue(
        callable $getDataCallback,
        string $cacheId,
        int|string $key,
        int $ttl = self::DEFAULT_TTL,
    ): mixed {
        if ('' === $cacheId) {
            throw new SystemException('Cache id must not be empty.');
        }

        $cache = self::getManagedCache();
        $array = [];

        if ($cache->read($ttl, $cacheId)) {
            $array = $cache->get($cacheId);
        }

        if (!array_key_exists($key, $array)) {
            $array[$key] = $getDataCallback($key);
            $cache->set($cacheId, $array);
        }

        return $array[$key];
    }

    /**
     * Кэширует данные с поддержкой тегов для инвалидации.
     *
     * @param string[] $tags
     *
     * @throws SystemException
     */
    public static function rememberTagged(
        callable $getDataCallback,
        string $cacheId,
        string $cacheDir = '',
        array $tags = [],
        int $ttl = self::DEFAULT_TTL,
    ): mixed {
        if ('' === $cacheId) {
            throw new SystemException('Cache id must not be empty.');
        }

        $cache = self::getDataCache();

        if ($cache->initCache($ttl, $cacheId, $cacheDir)) {
            return $cache->getVars();
        }

        $cache->startDataCache();

        if ([] !== $tags) {
            $taggedCache = self::getTaggedCache();
            $taggedCache->startTagCache($cacheDir);
            foreach ($tags as $tag) {
                $taggedCache->registerTag($tag);
            }
        }

        $data = $getDataCallback();
        $cache->endDataCache($data);

        if ([] !== $tags) {
            $taggedCache->endTagCache();
        }

        return $data;
    }

    /**
     * Обновляет значения в тегированном кэше (для накопительного кэша).
     *
     * @param string[] $tags
     *
     * @throws SystemException
     */
    public static function updateTagged(
        string $cacheId,
        string $cacheDir,
        array $newData,
        array $tags = [],
        int $ttl = self::DEFAULT_TTL,
    ): array {
        $cache = self::getDataCache();
        $existingData = [];

        // Читаем существующий кэш
        if ($cache->initCache($ttl, $cacheId, $cacheDir)) {
            $existingData = (array)$cache->getVars();
        }

        $mergedData = array_merge($existingData, $newData);

        // ИСПРАВЛЕНИЕ: не делаем clean(), просто перезаписываем
        $cache->forceRewriting(false);
        $cache->initCache($ttl, $cacheId, $cacheDir);
        $cache->startDataCache();

        if ([] !== $tags) {
            $taggedCache = self::getTaggedCache();
            $taggedCache->startTagCache($cacheDir);
            foreach ($tags as $tag) {
                $taggedCache->registerTag($tag);
            }
        }

        $cache->endDataCache($mergedData);

        if ([] !== $tags) {
            $taggedCache->endTagCache();
        }

        return $mergedData;
    }

    private static function getManagedCache(): ManagedCache
    {
        return self::$managedCache ??= Application::getInstance()->getManagedCache();
    }

    private static function getTaggedCache(): TaggedCache
    {
        return self::$taggedCache ??= Application::getInstance()->getTaggedCache();
    }

    private static function getDataCache(): DataCache
    {
        return self::$dataCache ??= DataCache::createInstance();
    }
}
