<?php

namespace Asycle\Core\Cache\Handler;

/**
 * Date: 2017/4/29
 * Time: 15:32
 */
use Asycle\Core\Cache\CacheHandlerInterface;
class RedisHandler implements CacheHandlerInterface
{
    protected $keyPrefix = '';
    protected $redis = null;
    public function __construct(\Redis $redis,$keyPrefix = '')
    {
        $this->keyPrefix = $keyPrefix;
        $this->redis = $redis;
    }
    protected function wrapKey($key){
        return $this->keyPrefix . $key;
    }
    /**
     * 保存键值
     * @param string $key 键
     * @param string $value 值
     * @param int $ttl 存活时间,为0则永久保存
     * @return bool 是否保存成功
     */
    public function set(string $key, string $value, int $ttl = 0): bool
    {
        if ($ttl == 0) {
            return $this->redis->set($this->wrapKey($key), $value);
        }
        return $this->redis->setex($this->wrapKey($key), $ttl,$value);
    }

    /**
     * 获取键值
     * @param string $key
     * @param null $default
     * @return bool|mixed
     */
    public function get(string $key,$default = null)
    {
        $res = $this->redis->get($this->wrapKey($key));
        if (empty($res)) {
            return null;
        }
        return $res;
    }

    /**
     * 删除键值
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $this->redis->delete($this->wrapKey($key));
        return true;
    }

    /**
     * 保存多个键值，过期时间都一样
     * @param array $keysAndValues
     * @param int $ttl
     * @return bool
     */
    public function setMany(array $keysAndValues, int $ttl = 0): bool
    {
        $this->redis->multi(\Redis::MULTI);
        foreach ($keysAndValues as $key=>$value){
            $this->redis->set($this->wrapKey($key),$value,$ttl);
        }
        $this->redis->exec();
        return true;

    }

    /**
     * 返回多个键值，不存在则对应键值为false
     * @param array $keys
     * @param null $default
     * @return array|mixed
     */
    public function getMany(array $keys,$default = null)
    {

        $cacheKeys= array_map([$this,'wrapKey'],$keys);
        $values = $this->redis->mget($cacheKeys);
        return array_combine($keys,$values);
    }

    /**
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return int
     */
    public function increment(string $key, $value = 1,$ttl = 0)
    {
        if($value == 1){
            return $this->redis->incr($this->wrapKey($key));
        }else{
            return $this->redis->incrBy($this->wrapKey($key),$value);
        }

    }

    /**
     * @param string $key
     * @param int $value
     * @param int $ttl
     * @return int
     */
    public function decrement(string $key, $value = 1,$ttl = 0)
    {
        if($value == 1){
            return $this->redis->decr($this->wrapKey($key));
        }else{
            return $this->redis->decrBy($this->wrapKey($key),$value);
        }
    }

    /**
     * 清空所有缓存
     * @return bool
     */
    public function flush(): bool
    {
        return $this->redis->flushDB();
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
        $value = $this->redis->get($this->wrapKey($key));
        if($value){
            return $value;
        }
        $value = call_user_func($closure);
        $this->set($key,$value,$ttl);
        return $value;
    }
}