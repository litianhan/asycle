<?php

namespace Asycle\Core\Cache;

/**
 * Date: 2017/8/30
 * Time: 14:24
 */
class Cache
{
    protected static $handler = null;
    public static function setHandler(CacheHandlerInterface $handler){
        self::$handler = $handler;
    }

    /**
     * 保存键值,会覆盖旧值
     * @param string $key 键
     * @param string $value 值
     * @param int $ttl 存活时间,为0则永久保存
     * @return bool 是否保存成功
     */
    public static function set(string $key, string $value, int $ttl = 0): bool
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->set($key, $value, $ttl);
        }
        return false;
    }
    /**
     * 获取键值
     * @param string $key
     * @param null $default
     * @return bool|mixed
     */
    public static function get(string $key,$default = null)
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->get($key);
        }
        return $default;
    }

    /**
     * 删除键值
     * @param string $key
     * @return bool
     */
    public static function delete(string $key): bool
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->delete($key);
        }
        return false;
    }

    /**
     * 保存多个键值，过期时间都一样
     * @param array $keysAndValues
     * @param int $ttl
     * @return bool
     */
    public static function setMany(array $keysAndValues, int $ttl = 0): bool
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->setMany($keysAndValues,$ttl);
        }
        return false;
    }

    /**
     * 返回多个键值，不存在则对应键值为false
     * @param array $keys
     * @param null $default
     * @return array|mixed
     */
    public static function getMany(array $keys,$default = null)
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->getMany($keys);
        }
        return array_fill_keys($keys,$default);
    }

    /**
     * @param string $key
     * @param int $value
     * @return mixed
     */
    public static function increment(string $key, $value = 1)
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->increment($key,$value);
        }
        return false;
    }

    /**
     * @param string $key
     * @param int $value
     * @return mixed
     */
    public static function decrement(string $key, $value = 1)
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->decrement($key,$value);
        }
        return false;
    }

    /**
     * 清空所有缓存
     * @return bool
     */
    public static function flush(): bool
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->flush();
        }
        return false;
    }

    /**
     * 获取键值,不存在则更新并获取
     * @param string $key
     * @param int $ttl
     * @param \Closure $closure
     * @return mixed
     */
    public static function getOrRefresh(string $key, int $ttl = 0, \Closure $closure)
    {
        if (self::$handler instanceof CacheHandlerInterface) {
            return self::$handler->getOrRefresh($key,$ttl,$closure);
        }
        return false;
    }
}