<?php

namespace App\Cache;

use Illuminate\Support\Facades\View;

class InternalCache
{

    private SystemInternalCache $cache;

    public function __construct()
    {
        $this->cache = new SystemCache();
    }

    /**
     * Get items from the cache.
     *
     * @param string $key
     * @return mixed
     */
    function get(string $key): mixed
    {
        return $this->cache->get($key);
    }

    /**
     * Put items in the cache with a TTL.
     *
     * Use's environment's REDIS_KEY_EXPIRATION value if $expiration is null.
     *
     * @param string $key
     * @param mixed|null $value
     * @param int|null $expiration
     * @return bool $value
     */
    function set(string $key, $value = null, $expiration = null): bool
    {
        return $this->cache->set($key, $value, $expiration);
    }

    /**
     * Add a TTL attribute (time to live or time til expiration) to a Redis key.
     *
     * @param string $key
     * @param null $expiration
     * @return bool
     */
    function expire(string $key, $expiration = null): bool
    {
        return $this->cache->expire($key, $expiration);
    }

    /**
     * Delete Redis key's from the Cache.
     *
     * @param $key array|string
     * @return array
     */
    function delete($key): array
    {
        return $this->cache->delete($key);
    }

    /**
     * Determine if a redis key exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    function exists(string $key): bool
    {
        return $this->cache->exists($key);
    }

    /**
     * Determine if a redis key is missing from the cache.
     *
     * @param string $key
     * @return bool
     */
    function missing(string $key): bool
    {
        return $this->cache->missing($key);
    }

    /**
     * Render a view & cache its output for reuse.
     *
     * @param string $key
     * @param string $view
     * @param array $data
     * @param int|null $expiration
     * @return bool
     */
    function cacheView(string $key, string $view, array $data, int $expiration = null): bool
    {
        return $this->cache->set($key, View::make($view, $data)->render(), $expiration);
    }

    /**
     * Increment a Redis Key's value & return the new value.
     *
     * @param string $key
     * @param int $value
     * @param int|null $expiration
     * @return int
     */
    function increment(string $key, int $value = 1, int $expiration = null): int
    {
        return $this->cache->increment($key, $value, $expiration);
    }

    /**
     * Flush the redis cache of all keys with environment's prefix.
     *
     * @return array
     */
    function clearCache(): array
    {
        return $this->cache->clear();
    }

    /**
     * Pass a $callback function to be stored in the Cache for an amount of time.
     *
     * @param string $key
     * @param $callback
     * @param int|null $ttl
     * @return mixed
     */
    function remember(string $key, $callback, int $ttl = null): mixed
    {
        return $this->cache->remember($key, $callback, $ttl);
    }

    /**
     * @param string $key
     * @param $callback
     * @return void
     */
    public function subscribe(string $key, $callback): void
    {
        $this->cache->subscribe($key, $callback);
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public function publish(string $key, $value): void
    {
        $this->cache->publish($key, $value);
    }
}
