<?php

namespace Asycle\Core\Cache;
/**
 * Date: 2017/5/19
 * Time: 13:06
 */
interface CacheHandlerInterface
{
    /**
     * 设置键值,会覆盖旧值
     * @param string $key 键
     * @param string $value 值
     * @param int $ttl 存活时间,为0则永久保存
     * @return bool 是否保存成功
     */
    public function set(string $key, string $value, int $ttl = 0): bool;
    /**
     * 保存多个键值，过期时间都一样
     * @param array $keysAndValues
     * @param int $ttl
     * @return bool
     */
    public function setMany(array $keysAndValues, int $ttl = 0):bool;

    /**
     * 获取键值
     * @param string $key
     * @param null $default
     * @return bool|mixed
     */
    public function get(string $key,$default = null);

    /**
     * 返回多个键值，不存在则对应键值为false
     * @param array $keys
     * @param null $default
     * @return array|mixed
     */
    public function getMany(array $keys,$default = null);

    /**
     *
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return mixed
     */
    public function increment(string $key,$value = 1,$ttl = 0);

    /**
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return mixed
     */
    public function decrement(string $key,$value = 1,$ttl = 0);
    /**
     * 清空所有缓存
     * @return bool
     */
    public function flush():bool ;

    /**
     * 获取键值,不存在则更新并获取
     * @param string $key
     * @param int $ttl
     * @param \Closure $closure
     * @return mixed
     */
    public function getOrRefresh(string $key,int $ttl = 0,\Closure $closure);

    /**
     * 删除键值
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;
}