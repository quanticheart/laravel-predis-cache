<?php

namespace App\Cache;

interface SystemInternalCache
{

    /**
     * Retrieve an array of keys that begin with a prefix.
     *
     * @param string $prefix
     * @param bool $wildcard
     * @return array|false[]|string[] list of keys without prefix
     */
    function keys(string $prefix = '', bool $wildcard = true): array;


    /**
     * Get items from the cache.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;


    /**
     * Put items in the cache with a TTL.
     *
     * Use's environment's REDIS_KEY_EXPIRATION value if $expiration is null.
     *
     * @param string $key
     * @param null $value
     * @param int|null $expiration
     * @return bool
     */
    public function set(string $key, $value = null, int $expiration = null): bool;


    /**
     * Put an array of key value pairs into the cache with a TTL.
     *
     * @param array $array
     * @param int|null $expiration
     * @return array
     */
    public function setMany(array $array, int $expiration = null): array;


    /**
     * Add a TTL attribute (time to live or time til expiration) to a Redis key.
     *
     * @param string $key
     * @param null $expiration
     * @return bool
     */
    public function expire(string $key, $expiration = null): bool;


    /**
     * Delete Redis key's from the Cache.
     *
     * @param $keys array|string
     * @param bool $children
     * @return array
     */
    public function delete(array|string $keys, bool $children = true): array;


    /**
     * Determine if a redis key exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;


    /**
     * Determine if a redis key is missing from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function missing(string $key): bool;


    /**
     * Create a Redis Key with a null value if it is missing.
     *
     * @param string $key
     * @param null $value
     * @param null $expiration
     * @return bool
     */
    public function setIfMissing(string $key, $value = null, $expiration = null): bool;

    /**
     * Increment a Redis Key's value & return the new value.
     *
     * @param string $key
     * @param int $value
     * @param int|null $expiration
     * @return int
     */
    public function increment(string $key, int $value = 1, int $expiration = null): int;

    /**
     * Flush the redis cache of all keys with environment's prefix.
     *
     * @return array
     */
    public function clear(): array;

    /**
     * Pass a $callback function to be stored in the Cache for an amount of time.
     *
     * @param string $key
     * @param $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function remember(string $key, $callback, int $ttl = null): mixed;


    /**
     * @param string $key
     * @param $callback
     */
    public function subscribe(string $key, $callback);


    /**
     * @param string $key
     * @param $value
     */
    public function publish(string $key, $value);





}
