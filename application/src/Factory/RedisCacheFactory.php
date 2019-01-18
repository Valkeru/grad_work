<?php

namespace App\Factory;

use Predis\Client as Predis;
use Symfony\Component\Cache\Simple\RedisCache;

class RedisCacheFactory
{
    public static function makeRedisCache(Predis $redis, int $ttl): RedisCache
    {
        return new RedisCache($redis, '', $ttl);
    }
}
