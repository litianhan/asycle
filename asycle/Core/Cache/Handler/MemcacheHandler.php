<?php

namespace Asycle\Core\Cache\Handler;
/**
 * Date: 2017/9/5
 * Time: 20:48
 */
use Asycle\Core\Cache\CacheHandlerInterface;
class MemcacheHandler implements CacheHandlerInterface{

    protected $memcached = null;
    public function __construct( \Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * 保存键值,会覆盖旧值
     * @param string $key 键
     * @param string $value 值
     * @param int $ttl 存活时间,为0则永久保存
     * @return bool 是否保存成功
     */
    public function set(string $key, string $value, int $ttl = 0): bool
    {
        return $this->memcached->set($key,$value,$ttl);
    }

    /**
     * 获取键值
     * @param string $key
     * @param null $default
     * @return bool|mixed
     */
    public function get(string $key,$default = null)
    {
        return $this->memcached->get($key) ?? $default;
    }

    /**
     * 删除键值
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->memcached->delete($key);
    }

    /**
     * 获取键值,不存在则更新并获取
     * @param string $key
     * @param int $ttl
     * @param \Closure $closure
     * @return mixed
     */
    public function getOrRefresh(string $key, int $ttl = 0, \Closure $closure)
    {
        $value = $this->memcached->get($key);
        if($value){
            return $value;
        }
        $value = $closure();
        $this->set($key,$value,$ttl);
        return $value;
    }

    /**
     * 保存多个键值，过期时间都一样
     * @param array $keysAndValues
     * @param int $ttl
     * @return bool
     */
    public function setMany(array $keysAndValues, int $ttl = 0): bool
    {

        return $this->memcached->setMulti($keysAndValues, $ttl);
    }

    /**
     * 返回多个键值，不存在则对应键值为false
     * @param array $keys
     * @param null $default
     * @return array|mixed
     */
    public function getMany(array $keys,$default = null )
    {

        $null = null;
        $values = $this->memcached->getMulti($keys, $null, \Memcached::GET_PRESERVE_ORDER);
        if ($this->memcached->getResultCode() != 0) {
            return array_fill_keys($keys, $default);
        }
        return array_combine($keys, $values);
    }

    /**
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return int
     */
    public function increment(string $key, $value = 1,$ttl = 0)
    {
        return $this->memcached->increment($key, $value);
    }

    /**
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return int
     */
    public function decrement(string $key, $value = 1,$ttl = 0)
    {
        return $this->memcached->decrement($key, $value);
    }

    /**
     * 清空所有缓存
     * @return bool
     */
    public function flush(): bool
    {
        return $this->memcached->flush();
    }
}