<?php

namespace App\Cache;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class SystemCache implements SystemInternalCache
{
    /**
     * Retrieve the Redis Key TTL from the config.
     *
     * @return int
     */
    private function defaultTTL(): int
    {
        return config('redis-helpers.ttl', 3600);
    }

    /**
     * Retrieve a redis key with the application prefix prepended.
     *
     *  - used when interacting directly with a Redis client instead of the `Cache` facade
     *
     * @param string $key
     * @return string
     */
    private function keyWithPrefix(string $key): string
    {
        if (!is_null(config('cache.prefix'))) {
            return config('cache.prefix') . ":{$key}";
        }

        return $key;
    }

    /**
     * Retrieve a key's time to live in seconds.
     *
     * @param string $key
     * @return int
     */
    public function ttl(string $key): int
    {
        return Redis::connection()->command('TTL', [self::keyWithPrefix($key)]);
    }

    /**
     * Retrieve an array of keys that begin with a prefix.
     *
     * @param string $prefix
     * @param bool $wildcard
     * @return array|false[]|string[] list of keys without prefix
     */
    public function keys(string $prefix = '', bool $wildcard = true): array
    {
        try {
            return array_map(
            // Remove prefix from each key so that it's not concatenated twice
                function ($key) {
                    return substr($key, strlen(config('cache.prefix')) + 1);
                },

                // List of Redis key's matching pattern
                Redis::connection()
                    ->client()
                    ->keys(self::keyWithPrefix($prefix . ($wildcard ? '*' : '')))
            );
        } catch (Exception $e) {
            return [$prefix];
        }
    }

    /**
     * Get items from the cache.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return Cache::get($key);
    }

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
    public function set(string $key, $value = null, int $expiration = null): bool
    {
        // Store the $value in the Cache
        return Cache::put(
            $key,
            $value,
            $expiration ?? self::defaultTTL()
        );
    }

    /**
     * Put an array of key value pairs into the cache with a TTL.
     *
     * @param array $array
     * @param int|null $expiration
     * @return array
     */
    public function setMany(array $array, int $expiration = null): array
    {
        foreach ($array as $key => $value) {
            self::set($key, $value, $expiration);
        }

        return array_values($array);
    }

    /**
     * Add a TTL attribute (time to live or time til expiration) to a Redis key.
     *
     * @param string $key
     * @param null $expiration
     * @return bool
     */
    public function expire(string $key, $expiration = null): bool
    {
        // Use environment REDIS_KEY_EXPIRATION value if not set
        if (!$expiration) {
            $expiration = self::defaultTTL();
        }

        // Create a key value pair with a null value and a TTL if the key is missing
        if (self::missing($key)) {
            return self::set($key, null, $expiration);
        } // Create a key value pair with original value and a TTL if the key exists
        else {
            return self::set($key, self::get($key), $expiration);
        }
    }

    /**
     * Delete Redis key's from the Cache.
     *
     * @param $keys array|string
     * @param bool $children
     * @return array
     */
    public function delete(array|string $keys, bool $children = true): array
    {
        // Returns an array of deleted keys with success values
        return collect((array)$keys)
            ->flatMap(function (string $key) use ($children) {
                return self::keys($key, $children);
            })
            ->mapWithKeys(function (string $key) {
                return [$key => Cache::forget($key)];
            })
            ->toArray();
    }

    /**
     * Determine if a redis key exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Determine if a redis key is missing from the cache.
     *
     * @param string $key
     * @return bool
     */
    public function missing(string $key): bool
    {
        return Cache::missing($key);
    }

    /**
     * Create a Redis Key with a null value if it is missing.
     *
     * @param string $key
     * @param null $value
     * @param null $expiration
     * @return bool
     */
    public function setIfMissing(string $key, $value = null, $expiration = null): bool
    {
        // Create the redis Key with an expiration
        if (self::missing($key)) {
            self::set($key, $value, $expiration);

            return true;
        }

        // Not created
        return false;
    }

    /**
     * Increment a Redis Key's value & return the new value.
     *
     * @param string $key
     * @param int $value
     * @param int|null $expiration
     * @return int
     */
    public function increment(string $key, int $value = 1, int $expiration = null): int
    {
        // Create the Key if it's missing
        self::setIfMissing($key, 0, $expiration);

        // Increment the value & return the new value
        return Cache::increment($key, $value);
    }

    /**
     * Flush the redis cache of all keys with environment's prefix.
     *
     * @return array
     */
    public function clear(): array
    {
        return self::delete('');
    }

    /**
     * Pass a $callback function to be stored in the Cache for an amount of time.
     *
     * @param string $key
     * @param Closure $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function remember(string $key, $callback, int $ttl = null): mixed
    {
        return Cache::remember($key, $ttl ?? self::defaultTTL(), $callback);
    }
}
