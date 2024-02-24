<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Laravel Redis

- [Laravel redis](https://laravel.com/docs/10.x/redis).

## Cache Redis

- [Redis Documents](https://redis.io/docs/).
- [Redis commands](https://redis.io/commands/).

## Pub / Seb

- [pub-sub-in-laravel-in-depth-understanding](https://www.twilio.com/en-us/blog/pub-sub-in-laravel-in-depth-understanding)

## Other
- [How-to-Install-Redis-Caching-Engine-on-cPanel-server](https://manage.accuwebhosting.com/knowledgebase/4466/How-to-Install-Redis-Caching-Engine-on-cPanel-server.html)
- [caching-api-endpoints-with-laravel](https://serversideup.net/caching-api-endpoints-with-laravel/).
- [laravel-caching-redis](https://www.honeybadger.io/blog/laravel-caching-redis/).

### Difference between Cache and Redis facade in laravel?

The Cache facade lets you access the cache, so you can add/get/forget cache items. If you use redis as your cache driver
this will use your redis instance as the cache store.

The Redis facade lets you access a redis connection, not the cache, although these may actually be the same redis
instance depending on your config. This lets you access the pub/sub features of redis and interact with the redis
instance using redis commands https://redis.io/commands

To get a better look at what the facades can do you can look at the classes they resolve to. The Cache facade resolves
to Illuminate\Contracts\Cache\Repository and the Redis facade to Illuminate\Redis\Connections\Connection.

More info on what classes the facades resolve to at https://laravel.com/docs/6.x/facades#facade-class-reference

While they may serve the same purpose, adding redis to your project you will offload the caching to a different server.
Thus reducing the load of your app server.

It mostly depends on your setup and the expected load:

- If it's simple project without much traffic or queries, you can keep using codeigniter's caching.
- If you expect lots of traffic or tons of SQL/NoSQL queries, it's best to offload the caching to a dedicated redis
  server/service to keep it running smoothly. This adds some complexity to the project of course.

If you are interested in reading more points of view, this post has some good points on Redis as to when to use it or
not: https://stackoverflow.com/a/3967052/9442192

When you use the cache facade, you will use the default settings under config\cache by default

When you specify to use redis store, you will use cache connection by default

Therefore you need to use something like Cache::driver($yourStoreName)->get($yourKeyName)

Exp:
Redis::connection('cache')->get($yourKeyName);

